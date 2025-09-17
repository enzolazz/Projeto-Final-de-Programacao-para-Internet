<?php

class EntityNotFoundException extends Exception
{
    public function __construct(string $entity, string $field, string $value)
    {
        $message = sprintf('%s não encontrado para %s = %s', $entity, $field, $value);
        parent::__construct($message, 404);
    }
}

class PermissionNotFoundException extends Exception {}
