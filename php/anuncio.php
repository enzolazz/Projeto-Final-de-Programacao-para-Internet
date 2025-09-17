<?php

require 'exceptions.php';

class Anuncio
{
    public static function Create(PDO $pdo, string $marca, string $modelo, int $ano, string $cor, int $quilometragem, string $descricao, float $valor, string $estado, string $cidade, array $fotos, int $idAnunciante): void
    {
        try {
            $pdo->beginTransaction();

            $stmt1 = $pdo->prepare(
                <<<'SQL'
            INSERT INTO anuncio (marca, modelo, ano, cor, quilometragem, descricao, valor, estado, cidade, idAnunciante)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            SQL
            );

            $stmt1->execute([$marca, $modelo, $ano, $cor, $quilometragem, $descricao, $valor, $estado, $cidade, $idAnunciante]);

            $idAnuncio = $pdo->lastInsertId();

            $stmt2 = $pdo->prepare(
                <<<'SQL'
            INSERT INTO foto (nomeArquivo, idAnuncio)
            VALUES (?, ?)
            SQL
            );

            foreach ($fotos as $nomeArquivo) {
                $stmt2->execute([$nomeArquivo, $idAnuncio]);
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new RuntimeException('Falha ao criar anúncio: ' . $e->getMessage());
        }
    }

    public static function Get(PDO $pdo, int $id): object
    {
        $sql = 'SELECT a.id, a.marca, a.modelo, a.ano, a.cor, a.quilometragem,
                   a.descricao, a.valor, a.dataHora, a.estado, a.cidade,
                   GROUP_CONCAT(f.nomeArquivo) AS fotos
            FROM anuncio a
            LEFT JOIN foto f ON f.idAnuncio = a.id
            WHERE a.id = ?
            GROUP BY a.id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() == 0) {
            throw new EntityNotFoundException('Anuncio', 'id', $id);
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $row['fotos'] = ! empty($row['fotos']) ? explode(',', $row['fotos']) : [];

        return (object) $row;
    }

    public static function GetMarcas(PDO $pdo): array
    {
        $sql = <<<'SQL'
      SELECT DISTINCT marca
      FROM anuncio
    SQL;

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new Exception('Não existem anuncios');
        }

        $arrayMarcas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $arrayMarcas;
    }

    public static function GetModelos(PDO $pdo, string $marca): array
    {
        $sql = <<<'SQL'
      SELECT DISTINCT modelo
      FROM anuncio
      WHERE marca = ?
      ORDER BY modelo
    SQL;

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$marca]);

        $arrayModelos = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $arrayModelos;
    }

    public static function GetCidades(PDO $pdo, array $filters): array
    {
        $sql = 'SELECT DISTINCT cidade
            FROM anuncio
            WHERE 1=1';

        $params = [];

        if (! empty($filters['marca'])) {
            $sql .= ' AND marca = :marca';
            $params[':marca'] = $filters['marca'];
        }

        if (! empty($filters['modelo'])) {
            $sql .= ' AND modelo = :modelo';
            $params[':modelo'] = $filters['modelo'];
        }

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new Exception('Não existem mais anúncios cadastrados.');
        }

        $arrayCidades = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $arrayCidades;
    }

    public static function GetFotos(PDO $pdo, int $idAnuncio): array
    {
        $stmt = $pdo->prepare(
            <<<'SQL'
        SELECT nomeArquivo
        FROM foto
        WHERE idAnuncio = ?
        ORDER BY id
        SQL
        );

        $stmt->execute([$idAnuncio]);

        $fotos = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $fotos ?: [];
    }

    public static function GetNFilter(PDO $pdo, array $filters, int $limit): array
    {
        $offset = $filters['offset'] ?? 0;

        $sql = 'SELECT a.id, a.marca, a.modelo, a.ano, a.valor, a.dataHora, a.cidade,
                   GROUP_CONCAT(f.nomeArquivo) AS fotos
            FROM anuncio a
            LEFT JOIN foto f ON f.idAnuncio = a.id
            WHERE 1=1';

        $params = [];

        if (! empty($filters['marca'])) {
            $sql .= ' AND a.marca = :marca';
            $params[':marca'] = $filters['marca'];
        }
        if (! empty($filters['modelo'])) {
            $sql .= ' AND a.modelo = :modelo';
            $params[':modelo'] = $filters['modelo'];
        }
        if (! empty($filters['cidade'])) {
            $sql .= ' AND a.cidade = :cidade';
            $params[':cidade'] = $filters['cidade'];
        }

        $sql .= ' GROUP BY a.id ORDER BY a.dataHora DESC LIMIT :limit OFFSET :offset';

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            throw new Exception('Não existem mais anúncios cadastrados.');
        }

        $arrayAnuncios = array_map(function ($row) {
            $row['fotos'] = ! empty($row['fotos']) ? explode(',', $row['fotos']) : [];

            return (object) $row;
        }, $rows);

        $lastOffset = $offset + count($arrayAnuncios);

        $countSql = 'SELECT COUNT(*) AS total FROM anuncio a WHERE 1=1';
        if (! empty($filters['marca'])) {
            $countSql .= ' AND a.marca = :marca';
        }
        if (! empty($filters['modelo'])) {
            $countSql .= ' AND a.modelo = :modelo';
        }
        if (! empty($filters['cidade'])) {
            $countSql .= ' AND a.cidade = :cidade';
        }

        $countStmt = $pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        if ($lastOffset >= $total) {
            $lastOffset = 0;
        }

        return [$arrayAnuncios, $lastOffset];
    }

    public static function GetByAnunciante(PDO $pdo, int $idAnunciante): array
    {
        $stmt = $pdo->prepare(
            <<<SQL
        SELECT a.id, a.marca, a.modelo, a.ano, a.valor, a.dataHora,
               GROUP_CONCAT(f.nomeArquivo) AS fotos
        FROM anuncio a
        LEFT JOIN foto f ON f.idAnuncio = a.id
        WHERE a.idAnunciante = ?
        GROUP BY a.id
        ORDER BY a.dataHora DESC
        SQL
        );

        $stmt->execute([$idAnunciante]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return [];
        }

        $arrayAnuncios = array_map(function ($row) {
            $row['fotos'] = ! empty($row['fotos']) ? explode(',', $row['fotos']) : [];

            return (object) $row;
        }, $rows);

        return $arrayAnuncios;
    }

    public static function Delete(PDO $pdo, int $idAnuncio, int $idAnunciante): void
    {
        $stmt = $pdo->prepare('DELETE FROM anuncio WHERE id = ? AND idAnunciante = ?');
        $stmt->execute([$idAnuncio, $idAnunciante]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('Anúncio não encontrado ou você não tem permissão para excluí-lo.');
        }
    }

    public static function GetInteresses(PDO $pdo, int $idAnuncio, int $idAnunciante): array
    {
        $stmtExists = $pdo->prepare('SELECT id FROM anuncio WHERE id = ?');
        $stmtExists->execute([$idAnuncio]);

        if ($stmtExists->rowCount() === 0) {
            throw new EntityNotFoundException('Anuncio', 'id', $idAnuncio);
        }

        $stmtCheck = $pdo->prepare('SELECT id FROM anuncio WHERE id = ? AND idAnunciante = ?');
        $stmtCheck->execute([$idAnuncio, $idAnunciante]);

        if ($stmtCheck->rowCount() === 0) {
            throw new PermissionNotFoundException('Você não possui permissões para este anuncio.');
        }

        $stmt = $pdo->prepare(
            <<<'SQL'
        SELECT id, nome, telefone, mensagem, dataHora
        FROM interesse
        WHERE idAnuncio = ?
        ORDER BY dataHora DESC
        SQL
        );

        $stmt->execute([$idAnuncio]);

        $interesses = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $interesses ?: [];
    }
}
