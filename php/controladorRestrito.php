<?php

require 'conexaoMysql.php';
require 'validacoes.php';
require 'sessionVerification.php';
require 'anunciante.php';
require 'anuncio.php';
require 'interesse.php';

class ControladorRestrito
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handle(string $acao): void
    {
        session_start();
        exitWhenNotLoggedIn();

        switch ($acao) {
            case 'adicionarAnuncio':
                $this->adicionarAnuncio();
                break;
            case 'alterarSenha':
                $this->alterarSenha();
                break;
            case 'meusAnuncios':
                $this->meusAnuncios();
                break;
            case 'excluirAnuncio':
                $this->excluirAnuncio();
                break;
            case 'meusInteresses':
                $this->meusInteresses();
                break;
            case 'excluirInteresse':
                $this->excluirInteresse();
                break;
            default:
                exit('Ação não disponível');
        }
    }

    private function excluirInteresse(): void
    {
        $id = $_POST['id'] ?? null;

        if (! $id) {
            jsonResponse(['erro' => 'ID do interesse não fornecido.'], 400);
        }

        try {
            Interesse::Remove($this->pdo, (int) $id);
            jsonResponse(['sucesso' => true]);
        } catch (Exception $e) {
            jsonResponse(['erro' => 'Erro ao excluir interesse: ' . $e->getMessage()], 500);
        }
    }

    private function meusInteresses(): void
    {
        $idAnuncio = $_GET['id'] ?? null;
        if (! $idAnuncio) {
            jsonResponse(['erro' => 'ID do anúncio não fornecido.'], 400);
        }

        try {
            $interesses = Anuncio::GetInteresses($this->pdo, (int) $idAnuncio, $_SESSION['user']->id);
            foreach ($interesses as $interesse) {
                $interesse->nome = htmlspecialchars($interesse->nome);
                $interesse->mensagem = htmlspecialchars($interesse->mensagem);
                $interesse->telefone = htmlspecialchars($interesse->telefone);
            }

            jsonResponse(['interesses' => $interesses]);
        } catch (EntityNotFoundException $e) {
            jsonResponse(['erro' => 'Erro ao buscar interesses: ' . $e->getMessage()], 404);
        } catch (PermissionNotFoundException $e) {
            jsonResponse(['erro' => 'Erro ao buscar interesses: ' . $e->getMessage()], 403);
        }

    }

    private function adicionarAnuncio(): void
    {

        [$campos, $erros] = validarCamposObrigatorios(
            ['marca', 'modelo', 'ano', 'cor', 'quilometragem', 'descricao', 'valor', 'estado', 'cidade'],
            $_POST
        );

        $fotos = $_FILES['fotos'] ?? null;
        if (! $fotos || ! isset($fotos['name']) || count(array_filter($fotos['name'])) < 3) {
            $erros['fotos'] = 'É necessário enviar pelo menos 3 fotos.';
        }

        if (! empty($erros)) {
            jsonResponse(['erros' => $erros], 400);
        }

        $campos['descricao'] = str_replace("'", "''", $campos['descricao']);

        try {
            $fotosSalvas = validaFotos($fotos);

            Anuncio::Create(
                $this->pdo,
                strtolower($campos['marca']),
                strtolower($campos['modelo']),
                $campos['ano'],
                $campos['cor'],
                $campos['quilometragem'],
                $campos['descricao'],
                $campos['valor'],
                $campos['estado'],
                strtolower($campos['cidade']),
                $fotosSalvas,
                $_SESSION['user']->id
            );

            jsonResponse(['sucesso' => true, 'redirect' => '/anuncios']);
        } catch (RuntimeException $e) {
            jsonResponse(['erro' => $e->getMessage()], 400);
        } catch (Exception $e) {
            jsonResponse(['erro' => 'Erro inesperado: ' . $e->getMessage()], 500);
        }
    }

    private function alterarSenha(): void
    {
        if (! isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            exit('Operação não permitida.');
        }

        $email = $_POST['email'] ?? '';
        $novaSenha = $_POST['novaSenha'] ?? '';
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        try {
            $idEsperado = Anunciante::GetSessionUser($this->pdo, $email)->id;

            if ($idEsperado != $_SESSION['user']->id) {
                jsonResponse(['erro' => 'E-mail incorreto.'], 400);
            }

            Anunciante::ChangePassword($this->pdo, $idEsperado, $senhaHash);

        } catch (Exception $e) {
            exit('Falha inesperada: ' . $e->getMessage());
        }

        jsonResponse(['sucesso' => true, 'redirect' => '/painel']);
    }

    private function meusAnuncios(): void
    {
        try {
            $anuncios = Anuncio::GetByAnunciante($this->pdo, $_SESSION['user']->id);

            foreach ($anuncios as $anuncio) {
                $anuncio->marca = ucwords(htmlspecialchars($anuncio->marca));
                $anuncio->modelo = ucwords(htmlspecialchars($anuncio->modelo));
                $anuncio->ano = htmlspecialchars($anuncio->ano);
                $anuncio->valor = number_format(htmlspecialchars($anuncio->valor), 2, ',', '.');
            }

            jsonResponse(['anuncios' => $anuncios]);
        } catch (Exception $e) {
            jsonResponse(['erro' => 'Erro ao buscar anúncios: ' . $e->getMessage()], 500);
        }
    }

    private function excluirAnuncio(): void
    {
        $id = $_POST['id'] ?? null;

        try {
            Anuncio::Delete($this->pdo, (int) $id, $_SESSION['user']->id);
            jsonResponse(['sucesso' => true]);
        } catch (Exception $e) {
            jsonResponse(['erro' => 'Erro ao excluir anúncio: ' . $e->getMessage()], 500);
        }
    }
}

$acao = $_GET['acao'] ?? '';
$pdo = mysqlConnect();
$controlador = new ControladorRestrito($pdo);
$controlador->handle($acao);
