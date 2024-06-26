<?php

declare(strict_types=1);

namespace App\Core\Features\Auth\RequestValidators;

use App\Core\Contracts\RequestValidatorInterface;
use App\Core\Exception\ValidationException;
use Valitron\Validator;

class UserLoginRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['email', 'password'])->message('Required field');
        $v->rule('email', 'email');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
