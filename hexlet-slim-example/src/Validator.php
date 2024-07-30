<?php

namespace Sasha\Slim;

class Validator
{
    public function validate($data)
    {
        $errors = [];
        if ((strlen($data['name']) < 2) || strlen($data['name']) > 10) {
            $errors['name'] = 'name should be from 2 to 10 symbols';
        }
        if (!preg_match('([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)', $data['email'])) {
            $errors['email'] = 'email should be like example@mail.com';
        }
        return $errors;
    }
}