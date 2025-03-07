<?php

namespace App\Services;

use Validator;

class AppService
{
    public static function validateRequest(array $data, array $rules): array|null
    {
        $validator = Validator::make($data, $rules);
    
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => self::formatErrors($validator->errors()->toArray()),
                'status_code' => 422,
            ];
        }

        return null;
    }

    private static function formatErrors(array $errors): array
    {
        return array_reduce($errors, function ($carry, $messages) {
            return array_merge($carry, $messages);
        }, []);
    }
}
