<?php
namespace Pyncer\Snyppet\Access\Component\Module\Token;

use DateInterval;
use DateTime;
use DateTimeZone;
use Psr\Http\Message\UriInterface as PsrUriInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractModule;
use Pyncer\Data\MapperQuery\OptionsQueryParam;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Http\Message\JsonResponse;
use Pyncer\Http\Message\Status;
use Pyncer\Snyppet\Access\Component\Forge\Token\TokenElementTrait;
use Pyncer\Snyppet\Access\Table\Token\TokenMapper;
use Pyncer\Snyppet\Access\Table\Token\TokenModel;
use Pyncer\Snyppet\Access\User\AccessManager;
use Pyncer\Snyppet\Access\User\LoginMethod;
use Pyncer\Utility\Token;

use const Pyncer\DATE_TIME_FORMAT as PYNCER_DATE_TIME_FORMAT;
use const Pyncer\Snyppet\Access\ALLOW_GUEST_ACCESS as PYNCER_ACCESS_ALLOW_GUEST_ACCESS;
use const Pyncer\Snyppet\Access\DEFAULT_REALM as PYNCER_ACCESS_DEFAULT_REALM;
use const Pyncer\Snyppet\Access\DEFAULT_SCHEME as PYNCER_ACCESS_DEFAULT_SCHEME;
use const Pyncer\Snyppet\Access\LOGIN_METHOD as PYNCER_ACCESS_LOGIN_METHOD;
use const Pyncer\Snyppet\Access\LOGIN_TOKEN_EXPIRATION as PYNCER_ACCESS_LOGIN_TOKEN_EXPIRATION;

class PostTokenItemModule extends AbstractModule
{
    use TokenElementTrait;

    protected ?RoutingPathInterface $idRoutingPath = null;

    public function getIdRoutingPath(): ?RoutingPathInterface
    {
        return $this->idRoutingPath;
    }
    public function setIdRoutingPath(?RoutingPathInterface $value): static
    {
        $this->idRoutingPath = $value;
        return $this;
    }

    protected function initializeAccessManager(): AccessManager
    {
        $connection = $this->get(ID::DATABASE);
        return new AccessManager($connection);
    }

    protected function getPrimaryResponse(): PsrResponseInterface
    {
        $connection = $this->get(ID::DATABASE);
        $snyppetManager = $this->get(ID::SNYPPET);

        $access = $this->initializeAccessManager();

        $loginMethod = PYNCER_ACCESS_LOGIN_METHOD;
        $allowGuestAccess = PYNCER_ACCESS_ALLOW_GUEST_ACCESS;
        $loginTokenExpiration = PYNCER_ACCESS_LOGIN_TOKEN_EXPIRATION;

        if ($snyppetManager->has('config')) {
            $config = $this->get(ID::config());

            $loginMethod = $config->getStr(
                'user_login_method',
                $loginMethod->value
            );
            $loginMethod = LoginMethod::from($loginMethod);

            $allowGuestAccess = $config->getBool(
                'user_allow_guest_access',
                $allowGuestAccess
            );

            $loginTokenExpiration = $config->getInt(
                'user_login_token_expiration',
                $loginTokenExpiration
            );
        }

        $loginValue = $this->parsedBody->getStr($loginMethod->value);
        $passwordValue = $this->parsedBody->getStr('password');

        if ($loginValue !== '' || $passwordValue  !== '') {
            $loginResult = $access->loginWithCredentials(
                $loginValue,
                $passwordValue,
                $loginMethod
            );

            if (!$loginResult) {
                return new JsonResponse(
                    Status::CLIENT_ERROR_422_UNPROCESSABLE_ENTITY,
                    [
                        'errors' => ['general' => 'invalid']
                    ]
                );
            }
        }

        if ($access->isGuest() && !$allowGuestAccess) {
            return new JsonResponse(
                Status::CLIENT_ERROR_422_UNPROCESSABLE_ENTITY,
                [
                    'errors' => ['general' => 'denied']
                ]
            );
        }

        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        $dateTime->add(new DateInterval('PT' . $loginTokenExpiration . 'S'));

        $model = new TokenModel([
            'user_id' => $access->getUserId(),
            'scheme' => $this->getScheme() ?? PYNCER_ACCESS_DEFAULT_SCHEME,
            'realm' => $this->getRealm() ?? PYNCER_ACCESS_DEFAULT_REALM,
            'token' => new Token(),
            'expiration_date_time' => $dateTime
        ]);

        $mapper = new TokenMapper($connection);
        $mapper->insert($model);

        $expirationDateTime = $model->getExpirationDateTime()
            ->format(PYNCER_DATE_TIME_FORMAT);

        $data = [
            'token' => $model->getToken(),
            'expiration_date_time' => $expirationDateTime,
        ];

        $options = new OptionsQueryParam($this->queryParams->getStr('$options'));
        if ($options->hasOption('include-user')) {
            $userModel = $access->getUser();

            $data['user'] = $this->getResponseUserData($userModel);
        }

        return (new JsonResponse(
            Status::SUCCESS_201_CREATED,
            $data,
        ))->withAddedHeader(
            'Location',
            $this->getResourceUrl($model)
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

    protected function getResourceUrl(TokenModel $model): PsrUriInterface
    {
        $url = $this->request->getUri();

        $idRoutingPath = $this->getIdRoutingPath()?->getRouteDirPath() ?? '@id64';
        if ($idRoutingPath === '@id64') {
            $path = $url->getPath() . '/' . $model->getToken();
        } elseif ($idRoutingPath === '@id') {
            $path = $url->getPath() . '/' . $model->getId();
        } else {
            throw new UnexpectedValueException(
                'Id routing path is not supported. (' . $idRoutingPath . ')'
            );
        }

        return $url->withPath($path);
    }
}
