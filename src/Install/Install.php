<?php
namespace Pyncer\Snyppet\Access\Install;

use Pyncer\Database\Table\Column\IntSize;
use Pyncer\Database\Table\ReferentialAction;
use Pyncer\Database\Value;
use Pyncer\Snyppet\AbstractInstall;
use Pyncer\Snyppet\Config\ConfigManager;

use const Pyncer\Snyppet\Access\ALLOW_GUEST_ACCESS as PYNCER_ACCESS_ALLOW_GUEST_ACCESS;
use const Pyncer\Snyppet\Access\DEFAULT_REALM as PYNCER_ACCESS_DEFAULT_REALM;
use const Pyncer\Snyppet\Access\LOGIN_METHOD as PYNCER_ACCESS_LOGIN_METHOD;
use const Pyncer\Snyppet\Access\LOGIN_TOKEN_EXPIRATION as PYNCER_ACCESS_LOGIN_TOKEN_EXPIRATION;

class Install extends AbstractInstall
{
    /**
     * @inheritdoc
     */
    protected function safeInstall(): bool
    {
        $this->connection->createTable('user')
            ->serial('id')
            ->string('mark', 250)->null()->index()
            ->dateTime('insert_date_time')->default(Value::NOW)->index()
            ->dateTime('update_date_time')->null()->index()
            ->enum('group', ['guest', 'user', 'admin', 'super'])->default('user')->index()
            ->string('name', 50)->null()->index()
            ->string('email', 125)->null()->index()
            ->string('phone', 25)->null()->index()
            ->string('username', 50)->null()->index()
            ->string('password', 250)->null()
            ->bool('internal')->default(false)->index()
            ->bool('enabled')->default(false)->index()
            ->bool('deleted')->default(false)->index()
            ->execute();

        $query = $this->connection->createTable('token')
            ->serial('id')
            ->int('user_id', IntSize::BIG)->null()->index()
            ->string('scheme', 50)->index()
            ->string('realm', 250)->index()
            ->string('token', 96)->index()
            ->dateTime('expiration_date_time')->null()->index()
            ->foreignKey(null, 'user_id')
                ->references('user', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('user__value')
            ->serial('id')
            ->int('user_id', IntSize::BIG)->index()
            ->string('key', 50)->index()
            ->text('value')
            ->bool('preload')->default(false)->index()
            ->index('#unique', 'user_id', 'key')->unique()
            ->foreignKey(null, 'user_id')
                ->references('user', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function safeUninstall(): bool
    {
        if ($this->connection->hasTable('token')) {
            $this->connection->dropTable('token');
        }

        if ($this->connection->hasTable('user')) {
            $this->connection->dropTable('user');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function hasRelated(string $snyppetAlias): bool
    {
        switch ($snyppetAlias) {
            case 'config':
                return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function installRelated(string $snyppetAlias): bool
    {
        switch ($snyppetAlias) {
            case 'config':
                return $this->installConfig();
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function uninstallRelated(string $snyppetAlias): bool
    {
        switch ($snyppetAlias) {
            case 'config':
                return $this->installConfig();
        }

        return false;
    }

    protected function installConfig(): bool
    {
        $config = new ConfigManager($this->connection);

        if (!$config->has('user_allow_guest_access')) {
            $config->set('user_allow_guest_access', PYNCER_ACCESS_ALLOW_GUEST_ACCESS);
            $config->setPreload('user_allow_guest_access', true);
            $config->save('user_allow_guest_access');
        }

        if (!$config->has('user_login_method')) {
            $config->set('user_login_method', PYNCER_ACCESS_LOGIN_METHOD->value);
            $config->setPreload('user_login_method', true);
            $config->save('user_login_method');
        }

        if (!$config->has('user_login_token_expiration')) {
            $config->set('user_login_token_expiration', PYNCER_ACCESS_LOGIN_TOKEN_EXPIRATION);
            $config->setPreload('user_login_token_expiration', true);
            $config->save('user_login_token_expiration');
        }

        return true;
    }

    protected function uninstallConfig(): bool
    {
        $config = new ConfigManager($this->connection);

        if (!$config->has('user_allow_guest_access')) {
            $config->set('user_allow_guest_access', null);
            $config->save('user_allow_guest_access');
        }

        if (!$config->has('user_login_method')) {
            $config->set('user_login_method', null);
            $config->save('user_login_method');
        }

        if (!$config->has('user_login_token_expiration')) {
            $config->set('user_login_token_expiration', null);
            $config->save('user_login_token_expiration');
        }

        return true;
    }
}
