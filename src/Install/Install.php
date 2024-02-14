<?php
namespace Pyncer\Snyppet\Access\Install;

use Pyncer\Database\Table\Column\IntSize;
use Pyncer\Database\Table\ReferentialAction;
use Pyncer\Database\Value;
use Pyncer\Snyppet\AbstractInstall;
use Pyncer\Snyppet\Access\Table\User\UserMapper;
use Pyncer\Snyppet\Access\Table\User\UserModel;
use Pyncer\Snyppet\Access\User\UserGroup;
use Pyncer\Snyppet\Config\ConfigManager;

use const Pyncer\Snyppet\Access\ALLOW_GUEST_ACCESS as PYNCER_ACCESS_ALLOW_GUEST_ACCESS;
use const Pyncer\Snyppet\Access\LOGIN_METHOD as PYNCER_ACCESS_LOGIN_METHOD;
use const Pyncer\Snyppet\Access\LOGIN_TOKEN_EXPIRATION as PYNCER_ACCESS_LOGIN_TOKEN_EXPIRATION;
use const Pyncer\Snyppet\Access\VALIDATE_LOGIN_NOT_FOUND as PYNCER_ACCESS_VALIDATE_LOGIN_NOT_FOUND;

use const Pyncer\Snyppet\Access\PASSWORD_CONFIRM_NEW as PYNCER_ACCESS_PASSWORD_CONFIRM_NEW;
use const Pyncer\Snyppet\Access\PASSWORD_CONFIRM_OLD as PYNCER_ACCESS_PASSWORD_CONFIRM_OLD;
use const Pyncer\Snyppet\Access\PASSWORD_MIN_LENGTH as PYNCER_ACCESS_PASSWORD_MIN_LENGTH;
use const Pyncer\Snyppet\Access\PASSWORD_MAX_LENGTH as PYNCER_ACCESS_PASSWORD_MAX_LENGTH;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_NUMERIC_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_NUMERIC_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_ALPHA_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_ALPHA_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_SPECIAL_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_SPECIAL_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_SPECIAL_CHARACTERS as PYNCER_ACCESS_PASSWORD_SPECIAL_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_ALLOW_WHITESPACE as PYNCER_ACCESS_PASSWORD_ALLOW_WHITESPACE;

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

        $userMapper = new UserMapper($this->connection);
        $userModel = new UserModel();
        $userModel->setGroup(UserGroup::GUEST);
        $userModel->setName('Guest');
        $userMapper->insert($userModel);

        $userMapper = new UserMapper($this->connection);
        $userModel = new UserModel();
        $userModel->setGroup(UserGroup::SUPER);
        $userModel->setName('Super');
        $userModel->setInternal(true);
        $userMapper->insert($userModel);

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

        if (!$config->has('user_validate_login_not_found')) {
            $config->set('user_validate_login_not_found', PYNCER_ACCESS_VALIDATE_LOGIN_NOT_FOUND);
            $config->setPreload('user_validate_login_not_found', true);
            $config->save('user_validate_login_not_found');
        }

        if (!$config->has('password_confirm_new')) {
            $config->set('password_confirm_new', PYNCER_ACCESS_PASSWORD_CONFIRM_NEW);
            $config->setPreload('password_confirm_new', true);
            $config->save('password_confirm_new');
        }

        if (!$config->has('password_confirm_old')) {
            $config->set('password_confirm_old', PYNCER_ACCESS_PASSWORD_CONFIRM_OLD);
            $config->setPreload('password_confirm_old', true);
            $config->save('password_confirm_old');
        }

        if (!$config->has('password_min_length')) {
            $config->set('password_min_length', PYNCER_ACCESS_PASSWORD_MIN_LENGTH);
            $config->setPreload('password_min_length', true);
            $config->save('password_min_length');
        }

        if (!$config->has('password_max_length')) {
            $config->set('password_max_length', PYNCER_ACCESS_PASSWORD_MAX_LENGTH);
            $config->setPreload('password_max_length', true);
            $config->save('password_max_length');
        }

        if (!$config->has('password_require_numeric_characters')) {
            $config->set('password_require_numeric_characters', PYNCER_ACCESS_PASSWORD_REQUIRE_NUMERIC_CHARACTERS);
            $config->setPreload('password_require_numeric_characters', true);
            $config->save('password_require_numeric_characters');
        }

        if (!$config->has('password_require_alpha_characters')) {
            $config->set('password_require_alpha_characters', PYNCER_ACCESS_PASSWORD_REQUIRE_ALPHA_CHARACTERS);
            $config->setPreload('password_require_alpha_characters', true);
            $config->save('password_require_alpha_characters');
        }

        if (!$config->has('password_require_lower_case_characters')) {
            $config->set('password_require_lower_case_characters', PYNCER_ACCESS_PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS);
            $config->setPreload('password_require_lower_case_characters', true);
            $config->save('password_require_lower_case_characters');
        }

        if (!$config->has('password_require_upper_case_characters')) {
            $config->set('password_require_upper_case_characters', PYNCER_ACCESS_PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS);
            $config->setPreload('password_require_upper_case_characters', true);
            $config->save('password_require_upper_case_characters');
        }

        if (!$config->has('password_require_special_characters')) {
            $config->set('password_require_special_characters', pyncer_access_password_require_special_characters);
            $config->setpreload('password_require_special_characters', true);
            $config->save('password_require_special_characters');
        }

        if (!$config->has('password_special_characters')) {
            $config->set('password_special_characters', PYNCER_ACCESS_PASSWORD_SPECIAL_CHARACTERS);
            $config->setPreload('password_special_characters', true);
            $config->save('password_special_characters');
        }

        if (!$config->has('password_allow_whitespace')) {
            $config->set('password_allow_whitespace', PYNCER_ACCESS_PASSWORD_ALLOW_WHITESPACE);
            $config->setPreload('password_allow_whitespace', true);
            $config->save('password_allow_whitespace');
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

        if (!$config->has('user_validate_login_not_found')) {
            $config->set('user_validate_login_not_found', null);
            $config->save('user_validate_login_not_found');
        }

        if (!$config->has('password_confirm_new')) {
            $config->set('password_confirm_new', null);
            $config->save('password_confirm_new');
        }

        if (!$config->has('password_confirm_old')) {
            $config->set('password_confirm_old', null);
            $config->save('password_confirm_old');
        }

        if (!$config->has('password_min_length')) {
            $config->set('password_min_length', null);
            $config->save('password_min_length');
        }

        if (!$config->has('password_max_length')) {
            $config->set('password_max_length', null);
            $config->save('password_max_length');
        }

        if (!$config->has('password_require_numeric_characters')) {
            $config->set('password_require_numeric_characters', null);
            $config->save('password_require_numeric_characters');
        }

        if (!$config->has('password_require_alpha_characters')) {
            $config->set('password_require_alpha_characters', null);
            $config->save('password_require_alpha_characters');
        }

        if (!$config->has('password_require_lower_case_characters')) {
            $config->set('password_require_lower_case_characters', null);
            $config->save('password_require_lower_case_characters');
        }

        if (!$config->has('password_require_upper_case_characters')) {
            $config->set('password_require_upper_case_characters', null);
            $config->save('password_require_upper_case_characters');
        }

        if (!$config->has('password_require_special_characters')) {
            $config->set('password_require_special_characters', null);
            $config->save('password_require_special_characters');
        }

        if (!$config->has('password_special_characters')) {
            $config->set('password_special_characters', null);
            $config->save('password_special_characters');
        }

        if (!$config->has('password_allow_whitespace')) {
            $config->set('password_allow_whitespace', null);
            $config->save('password_allow_whitespace');
        }

        return true;
    }
}
