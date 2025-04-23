<?php
namespace Pyncer\Snyppet\Access\User;

use Pyncer\Utility\ParamsInterface;
use Pyncer\Validation\Rule\PasswordRule;

use const Pyncer\Snyppet\Access\PASSWORD_CONFIRM_OLD as PYNCER_ACCESS_PASSWORD_CONFIRM_OLD;
use const Pyncer\Snyppet\Access\PASSWORD_CONFIRM_NEW as PYNCER_ACCESS_PASSWORD_CONFIRM_NEW;
use const Pyncer\Snyppet\Access\PASSWORD_MIN_LENGTH as PYNCER_ACCESS_PASSWORD_MIN_LENGTH;
use const Pyncer\Snyppet\Access\PASSWORD_MAX_LENGTH as PYNCER_ACCESS_PASSWORD_MAX_LENGTH;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_NUMERIC_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_NUMERIC_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_ALPHA_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_ALPHA_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_REQUIRE_SPECIAL_CHARACTERS as PYNCER_ACCESS_PASSWORD_REQUIRE_SPECIAL_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_SPECIAL_CHARACTERS as PYNCER_ACCESS_PASSWORD_SPECIAL_CHARACTERS;
use const Pyncer\Snyppet\Access\PASSWORD_ALLOW_WHITESPACE as PYNCER_ACCESS_PASSWORD_ALLOW_WHITESPACE;

class PasswordConfig
{
    protected ?int $minLength = null;
    protected ?int $maxLength = null;
    protected ?bool $requireNumericCharacters = null;
    protected ?bool $requireAlphaCharacters = null;
    protected ?bool $requireLowerCaseCharacters = null;
    protected ?bool $requireUpperCaseCharacters = null;
    protected ?bool $requireSpecialCharacters = null;
    protected ?string $specialCharacters = null;
    protected ?bool $allowWhitespace = null;
    protected ?bool $confirmNew = null;
    protected ?bool $confirmOld = null;

    public function __construct(
        protected ?ParamsInterface $config = null,
    ) {}

    public function getMinLength(): ?int
    {
        if ($this->minLength !== null) {
            return $this->minLength;
        }

        $minLength = PYNCER_ACCESS_PASSWORD_MIN_LENGTH;

        if ($this->config !== null) {
            $minLength = $this->config->getInt(
                'password_min_length',
                $minLength
            );
        }

        return $minLength;
    }
    public function setMinLength(?int $value): static
    {
        $this->minLength = $value;
        return $this;
    }

    public function getMaxLength(): ?int
    {
        if ($this->maxLength !== null) {
            return $this->maxLength;
        }

        $maxLength = PYNCER_ACCESS_PASSWORD_MAX_LENGTH;

        if ($this->config !== null) {
            $maxLength = $this->config->getInt(
                'password_max_length',
                $maxLength
            );
        }

        return $maxLength;
    }
    public function setMaxLength(?int $value): static
    {
        $this->maxLength = $value;
        return $this;
    }

    public function getRequireNumericCharacters(): ?bool
    {
        if ($this->requireNumericCharacters !== null) {
            return $this->requireNumericCharacters;
        }

        $requireNumericCharacters = PYNCER_ACCESS_PASSWORD_REQUIRE_NUMERIC_CHARACTERS;

        if ($this->config !== null) {
            $requireNumericCharacters = $this->config->getBool(
                'password_require_numeric_characters',
                $requireNumericCharacters
            );
        }

        return $requireNumericCharacters;
    }
    public function setRequireNumericCharacters(?bool $value): static
    {
        $this->requireNumericCharacters = $value;
        return $this;
    }

    public function getRequireAlphaCharacters(): ?bool
    {
        if ($this->requireAlphaCharacters !== null) {
            return $this->requireAlphaCharacters;
        }

        $requireAlphaCharacters = PYNCER_ACCESS_PASSWORD_REQUIRE_ALPHA_CHARACTERS;

        if ($this->config !== null) {
            $requireAlphaCharacters = $this->config->getBool(
                'password_require_alpha_characters',
                $requireAlphaCharacters
            );
        }

        return $requireAlphaCharacters;
    }
    public function setRequireAlphaCharacters(?bool $value): static
    {
        $this->requireAlphaCharacters = $value;
        return $this;
    }

    public function getRequireLowerCaseCharacters(): ?bool
    {
        if ($this->requireLowerCaseCharacters !== null) {
            return $this->requireLowerCaseCharacters;
        }

        $requireLowerCaseCharacters = PYNCER_ACCESS_PASSWORD_REQUIRE_LOWER_CASE_CHARACTERS;

        if ($this->config !== null) {
            $requireLowerCaseCharacters = $this->config->getBool(
                'password_require_lower_case_characters',
                $requireLowerCaseCharacters
            );
        }

        return $requireLowerCaseCharacters;
    }
    public function setRequireLowerCaseCharacters(?bool $value): static
    {
        $this->requireLowerCaseCharacters = $value;
        return $this;
    }

    public function getRequireUpperCaseCharacters(): ?bool
    {
        if ($this->requireUpperCaseCharacters !== null) {
            return $this->requireUpperCaseCharacters;
        }

        $requireUpperCaseCharacters = PYNCER_ACCESS_PASSWORD_REQUIRE_UPPER_CASE_CHARACTERS;

        if ($this->config !== null) {
            $requireUpperCaseCharacters = $this->config->getBool(
                'password_require_upper_case_characters',
                $requireUpperCaseCharacters
            );
        }

        return $requireUpperCaseCharacters;
    }
    public function setRequireUpperCaseCharacters(?bool $value): static
    {
        $this->requireUpperCaseCharacters = $value;
        return $this;
    }

    public function getRequireSpecialCharacters(): ?bool
    {
        if ($this->requireSpecialCharacters !== null) {
            return $this->requireSpecialCharacters;
        }

        $requireSpecialCharacters = PYNCER_ACCESS_PASSWORD_REQUIRE_SPECIAL_CHARACTERS;

        if ($this->config !== null) {
            $requireSpecialCharacters = $this->config->getBool(
                'password_require_special_characters',
                $requireSpecialCharacters
            );
        }

        return $requireSpecialCharacters;
    }
    public function setRequireSpecialCharacters(?bool $value): static
    {
        $this->requireSpecialCharacters = $value;
        return $this;
    }

    public function getSpecialCharacters(): ?string
    {
        if ($this->specialCharacters !== null) {
            return $this->specialCharacters;
        }

        $specialCharacters = PYNCER_ACCESS_PASSWORD_SPECIAL_CHARACTERS;

        if ($this->config !== null) {
            $specialCharacters = $this->config->getString(
                'password_special_characters',
                $specialCharacters
            );
        }

        return $specialCharacters;
    }
    public function setSpecialCharacters(?string $value): static
    {
        $this->specialCharacters = $value;
        return $this;
    }

    public function getAllowWhitespace(): ?bool
    {
        if ($this->allowWhitespace !== null) {
            return $this->allowWhitespace;
        }

        $allowWhitespace = PYNCER_ACCESS_PASSWORD_ALLOW_WHITESPACE;

        if ($this->config !== null) {
            $allowWhitespace = $this->config->getBool(
                'password_allow_whitespace',
                $allowWhitespace
            );
        }

        return $allowWhitespace;
    }
    public function setAllowWhitespace(?bool $value): static
    {
        $this->allowWhitespace = $value;
        return $this;
    }

    public function getConfirmNew(): ?bool
    {
        if ($this->confirmNew !== null) {
            return $this->confirmNew;
        }

        $confirmNew = PYNCER_ACCESS_PASSWORD_CONFIRM_NEW;

        if ($this->config !== null) {
            $confirmNew = $this->config->getBool(
                'password_confirm_new',
                $confirmNew
            );
        }

        return $confirmNew;
    }
    public function setConfirmNew(?bool $value): static
    {
        $this->confirmNew = $value;
        return $this;
    }

    public function getConfirmOld(): ?bool
    {
        if ($this->confirmOld !== null) {
            return $this->confirmOld;
        }

        $confirmOld = PYNCER_ACCESS_PASSWORD_CONFIRM_OLD;

        if ($this->config !== null) {
            $confirmOld = $this->config->getBool(
                'password_confirm_old',
                $confirmOld
            );
        }

        return $confirmOld;
    }
    public function setConfirmOld(?bool $value): static
    {
        $this->confirmOld = $value;
        return $this;
    }

    public function getValidationRule(): PasswordRule
    {
        return new PasswordRule(
            minLength: $this->getMinLength(),
            maxLength: $this->getMaxLength(),
            requireNumericCharacters: $this->getRequireNumericCharacters(),
            requireAlphaCharacters: $this->getRequireAlphaCharacters(),
            requireLowerCaseCharacters: $this->getRequireLowerCaseCharacters(),
            requireUpperCaseCharacters: $this->getRequireUpperCaseCharacters(),
            requireSpecialCharacters: $this->getRequireSpecialCharacters(),
            specialCharacters: $this->getSpecialCharacters(),
            allowWhitespace: $this->getAllowWhitespace(),
        );
    }
}
