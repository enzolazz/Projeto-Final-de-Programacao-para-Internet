<?php

enum LoginStatus
{
    case EMAIL_NOT_FOUND;
    case INCORRECT_PASSWORD;
    case SUCCESS;
}

class LoginResult implements JsonSerializable
{
    public LoginStatus $status;
    public string $newLocation;

    public function __construct(LoginStatus $status, string $newLocation = '')
    {
        $this->status = $status;
        $this->newLocation = $newLocation;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'status' => $this->status->name,
            'newLocation' => $this->newLocation,
        ];
    }
}

function checkUserCredentials($pdo, $email, $senha)
{
    $sql = <<<'SQL'
    SELECT senhaHash
    FROM anunciante
    WHERE email = ?
    SQL;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $senhaHash = $stmt->fetchColumn();

        if (! $senhaHash) {
            return LoginStatus::EMAIL_NOT_FOUND;
        }

        if (! password_verify($senha, $senhaHash)) {
            return LoginStatus::INCORRECT_PASSWORD;
        }

        return LoginStatus::SUCCESS;
    } catch (Exception $e) {
        exit('Falha inesperada: ' . $e->getMessage());
    }
}
