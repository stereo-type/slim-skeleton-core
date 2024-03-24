<?php

declare(strict_types = 1);

namespace App\Core\RequestValidators;

use App\Core\Contracts\RequestValidatorInterface;
use App\Core\Exception\ValidationException;
use Valitron\Validator;

class UpdateProfileRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', 'name')->message('Required field');
        $v->rule('integer', 'twoFactor')->message('Invalid Two-Factor indicator');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
