<?php
class Interesse
{
  static function Create($pdo, $nome, $telefone, $mensagem, $idAnuncio)
  {
    $stmt = $pdo->prepare(
      <<<SQL
      INSERT INTO interesse (nome, telefone, mensagem, idAnuncio)
      VALUES (?, ?, ?, ?)
      SQL
    );

    $stmt->execute([$nome, $telefone, $mensagem, $idAnuncio]);

    return $pdo->lastInsertId();
  }

  static function Get($pdo, $id)
  {
    $stmt = $pdo->prepare(
      <<<SQL
      SELECT id,  nome, telefone, mensagem, data_hora, idAnuncio
      FROM interesse
      WHERE id = ?
      SQL
    );

    $stmt->execute([$id]);
    if ($stmt->rowCount() == 0)
      throw new Exception("Interesse não localizado");

    $interesse = $stmt->fetch(PDO::FETCH_OBJ);
    return $interesse;
  }

  public static function Remove($pdo, $id)
  {
    $sql = <<<SQL
    DELETE 
    FROM interesse
    WHERE id = ?
    LIMIT 1
    SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
  }
}
