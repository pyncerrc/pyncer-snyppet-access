<?php
namespace Pyncer\Snyppet\Access\Component\Module\Token;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractModule;
use Pyncer\Data\MapperQuery\FiltersQueryParam;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\MapperQuery\OptionsQueryParam;
use Pyncer\Http\Message\JsonResponse;
use Pyncer\Http\Message\Response;
use Pyncer\Http\Message\Status;
use Pyncer\Snyppet\Access\Component\Forge\Token\TokenElementTrait;
use Pyncer\Snyppet\Access\Table\Token\TokenMapper;
use Pyncer\Snyppet\Access\Table\Token\TokenMapperQuery;
use Pyncer\Snyppet\Access\Table\Token\TokenModel;

use const Pyncer\DATE_TIME_FORMAT as PYNCER_DATE_TIME_FORMAT;
use const Pyncer\Snyppet\Access\REALM_SERVICE as PYNCER_ACCESS_DEFAULT_REALM;

class GetTokenItemModule extends AbstractModule
{
    use TokenElementTrait;

    protected function getPrimaryResponse(): PsrResponseInterface
    {
        $connection = $this->get(ID::DATABASE);

        $tokenMapper = new TokenMapper($connection);
        $tokenMapperQuery = $this->forgeMapperQuery();
        $tokenModel = null;

        $id = $this->queryParams->getInt('id', null);
        if ($id !== null) {
            $tokenModel = $tokenMapper->selectById($id, $tokenMapperQuery);
        } else {
            $id64 = $this->queryParams->getStr('id64', null);
            if ($id64 !== null) {
                $tokenModel = $tokenMapper->selectByToken(
                    'Bearer',
                    $this->getRealm() ?? PYNCER_ACCESS_DEFAULT_REALM,
                    $id64,
                    $tokenMapperQuery
                );
            }
        }

        if (!$tokenModel) {
            return new Response(
                Status::CLIENT_ERROR_404_NOT_FOUND
            );
        }

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
            $this->queryParams->getStr('$options')
        );
        $tokenMapperQuery->setOptions($options);

        // Filters
        $filters = 'scheme eq \'Bearer\' and ';

        $realm = $this->getRealm() ?? PYNCER_ACCESS_DEFAULT_REALM

        $filters .= 'realm eq \'' . $realm . '\'';

        $filters = new FiltersQueryParam($filters);
        $tokenMapperQuery->setFilter($filters);

        return $tokenMapperQuery;
    }
}
