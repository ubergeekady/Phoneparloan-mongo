<?php

namespace App\Validators;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

/**
 * Created by PhpStorm.
 * User: vishnu
 * Date: 28/08/18
 * Time: 1:00 PM
 */
class RestValidator extends Validator
{
    public function addFailure($attribute, $rule, $parameters=[])
    {
        $message = $this->getMessage($attribute, $rule);

        $message = $this->makeReplacements($message, $attribute, $rule, $parameters);

        $customMessage = new MessageBag();

        $customMessage->merge(['code' => strtolower($rule.'_rule_error')]);
        $customMessage->merge(['message' => $message]);

        $this->messages->add($attribute, $customMessage);

    }

}