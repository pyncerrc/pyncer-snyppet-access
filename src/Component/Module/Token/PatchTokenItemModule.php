<?php
namespace Pyncer\Snyppet\Access\Component\Module\Token;

use DateTime;
use DateInterval;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractModule;
use Pyncer\Data\MapperQuery\FiltersQueryParam;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\MapperQuery\OptionsQueryParam;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Http\Message\JsonResponse;
use Pyncer\Http\Message\Response;
use Pyncer\Http\Message\Status;
use Pyncer\Snyppet\Access\Component\Forge\Token\TokenElementTrait;
use Pyncer\Snyppet\Access\Table\Token\TokenMapper;
use Pyncer\Snyppet\Access\Table\Token\TokenMapperQuery;
use Pyncer\Snyppet\Access\Table\Token\TokenModel;

use const Pyncer\Snyppet\Access\DEFAULT_RELAM as PYNCER_ACCESS_DEFAULT_REALM;
use const Pyncer\Snyppet\Access\DEFAULT_SCHEME as PYNCER_ACCESS_DEFAULT_SCHEME;
use const Pyncer\Snyppet\Access\LOGIN_TOKEN_EXPIRATION as PYNCER_ACCESS_LOGIN_TOKEN_EXPIRATION;
use const Pyncer\DATE_TIME_FORMAT as PYNCER_DATE_TIME_FORMAT;
use const Pyncer\DATE_TIME_NOW as PYNCER_DATE_TIME_NOW;

class PatchTokenItemModule extends AbstractModule
{
    use TokenElementTrait;

    protected ?RoutingPathInterface $idRoutingPath = null;
    protected ?int $loginTokenExpiration = null;

    public function getIdRoutingPath(): ?RoutingPathInterface
    {
        return $this->idRoutingPath;
    }
    public function setIdRoutingPath(?RoutingPathInterface $value): static
    {
        $this->idRoutingPath = $value;
        return $this;
    }

    public function getLoginTokenExpiration(): int
    {
        if ($this->loginTokenExpiration !== null) {
            return $this->loginTokenExpiration;
        }

        $loginTokenExpiration = PYNCER_ACCESS_LOGIN_TOKEN_EXPIRATION;

        $snyppetManager = $this->get(ID::SNYPPET);
        if ($snyppetManager->has('config')) {
            $config = $this->get(ID::config());

            $loginTokenExpiration = $config->getInt(
                'user_login_token_expiration',
                $loginTokenExpiration
            );
        }

        return $loginTokenExpiration;
    }
    public function setLoginTokenExpiration(?int $value): static
    {
        $this->loginTokenExpiration = $value;
        return $this;
    }

    protected function getPrimaryResponse(): PsrResponseInterface
    {
        $connection = $this->get(ID::DATABASE);

        $loginTokenExpiration = $this->getLoginTokenExpiration();

        $tokenMapper = new TokenMapper($connection);
        $tokenMapperQuery = $this->forgeMapperQuery();
        $tokenModel = null;

        $idRoutingPath = $this->getIdRoutingPath()?->getRouteDirPath() ?? '@id64';
        if ($idRoutingPath === '@id64') {
            $id64 = $this->queryParams->getString(
                $this->getIdRoutingPath()?->getQueryName() ?? 'id64',
                null
            );

            if ($id64 !== null) {
                $tokenModel = $tokenMapper->selectByColumns(
                    ['token' => $id64],
                    $tokenMapperQuery
                );
            }
        } elseif ($idRoutingPath === '@id') {
            $id = $this->queryParams->getInt(
                $this->getIdRoutingPath()?->getQueryName() ?? 'id',
                null
            );

            if ($id !== null) {
                $tokenModel = $tokenMapper->selectById($id, $tokenMapperQuery);
            }
        } else {
            throw new UnexpectedValueException(
                'Id routing path is not supported. (' . $idRoutingPath . ')'
            );
        }

        if (!$tokenModel) {
            return new Response(
                Status::CLIENT_ERROR_404_NOT_FOUND
            );
        }

        if ($tokenModel->getExpirationDateTime() < PYNCER_DATE_TIME_NOW) {
            return new JsonResponse(
                Status::CLIENT_ERROR_401_UNAUTHORIZED,
                [
                    'errors' => ['general' => 'expired']
                ]
            );
        }

        $dateTime = new DateTime();
        $dateTime->add(new DateInterval('PT' . $loginTokenExpiration . 'S'));

        $tokenModel->setExpirationDateTime($dateTime);

        $tokenMapper->update($tokenModel);

        $expirationDateTime = $tokenModel->getExpirationDateTime()
            ->format(PYNCER_DATE_TIME_FORMAT);

        $data = [
            'token' => $tokenModel->getToken(),
            'expiration_date_time' => $expirationDateTime,
        ];

        if ($tokenMapperQuery->getOptions()->hasOption('include-user')) {
            $userModel = $tokenModel->getSideModel('user');

            $data['user'] = $this->getResponseUserData($userModel);
        }

        return new JsonResponse(
            Status::SUCCESS_200_OK,
            $data
        );
    }

    protected function getResponseUserData(ModelInterface $userModel): array
    {
        $data = $userModel->getData();

        unset(
            $data['mark'],
            $data['password'],
            $data['enabled'],
            $data['deleted']
        );

        return $data;
    }

    /**
    * @return \Pyncer\Data\MapperQuery\MapperQueryInterface
    */
    protected function forgeMapperQuery(): ?MapperQueryInterface
    {
        $connection = $this->get(ID::DATABASE);
        $tokenMapperQuery = new TokenMapperQuery($connection);

        // Options
        $options = new OptionsQueryParam(
            $this->queryParams->getString('$options')
        );
        $tokenMapperQuery->setOptions($options);

        // Filters
        $scheme = $this->getScheme() ?? PYNCER_ACCESS_DEFAULT_SCHEME;
        $filters = 'scheme eq \'' . $scheme . '\' and ';

        $realm = $this->getRealm() ?? PYNCER_ACCESS_DEFAULT_REALM;

        $filters .= 'realm eq \'' . $realm . '\'';

        $filters = new FiltersQueryParam($filters);
        $tokenMapperQuery->setFilters($filters);

        return $tokenMapperQuery;
    }
}
