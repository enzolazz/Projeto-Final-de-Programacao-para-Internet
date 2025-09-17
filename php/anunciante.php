<?php

class Anunciante
{
    public static function Create(PDO $pdo, string $nome, string $cpf, string $email, string $senhaHash, string $telefone): string
    {
        $stmt = $pdo->prepare(
            <<<'SQL'
      INSERT INTO anunciante (nome, cpf, email, senhaHash, telefone)
      VALUES (?, ?, ?, ?, ?)
      SQL
        );

        $stmt->execute([$nome, $cpf, $email, $senhaHash, $telefone]);

        return $pdo->lastInsertId();
    }

    public static function GetSessionUser(PDO $pdo, string $email): object
    {
        $stmt = $pdo->prepare(
            <<<'SQL'
      SELECT id, nome
      FROM  anunciante
      WHERE email = ?
      SQL
        );

        $stmt->execute([$email]);
        if ($stmt->rowCount() == 0) {
            throw new Exception('Anunciante não localizado');
        }

        $anunciante = $stmt->fetch(PDO::FETCH_OBJ);

        $primeiroNome = explode(' ', trim($anunciante->nome))[0];
        $anunciante->nome = $primeiroNome;

        return $anunciante;
    }

    public static function Get(PDO $pdo, string $id): string
    {
        $stmt = $pdo->prepare(
            <<<'SQL'
      SELECT id, nome, cpf, email, senhaHash, telefone
      FROM  anunciante
      WHERE id = ?
      SQL
        );

        $stmt->execute([$id]);
        if ($stmt->rowCount() == 0) {
            throw new Exception('Anunciante não localizado');
        }

        $anunciante = $stmt->fetch(PDO::FETCH_OBJ);

        return $anunciante;
    }

    public static function Remove(PDO $pdo, string $id): void
    {
        $sql = <<<'SQL'
    DELETE
    FROM anunciante
    WHERE id = ?
    LIMIT 1
    SQL;

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    }

    public static function ChangePassword(PDO $pdo, string $id, string $novaSenha): void
    {
        $sql = <<<'SQL'
      UPDATE anunciante
      SET senhaHash = ?
      WHERE id = ?
    SQL;

        $stmt = $pdo->prepare($sql
        );
        $stmt->execute([$novaSenha, $id]);
    }
}
