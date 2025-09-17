<?php

require 'conexaoMysql.php';
require 'interesse.php';
require 'anunciante.php';
require 'anuncio.php';
require 'login.php';
require 'validacoes.php';
require_once 'exceptions.php';

class Controlador
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handle(string $acao): void
    {
        switch ($acao) {
            case 'adicionarAnunciante':
                $this->adicionarAnunciante();
                break;
            case 'checarLogin':
                $this->checarLogin();
                break;
            case 'maisAnuncios':
                $this->maisAnuncios();
                break;
            case 'marcas':
                $this->carregarMarcas();
                break;
            case 'modelos':
                $this->carregarModelos();
                break;
            case 'cidades':
                $this->carregarCidades();
                break;
            case 'buscarAnuncio':
                $this->buscarAnuncio();
                break;
            case 'adicionarInteresse':
                $this->adicionarInteresse();
                break;
            default:
                exit('Ação não disponível');
        }
    }

    private function adicionarAnunciante(): void
    {
        [$campos, $erros] = validarCamposObrigatorios(
            ['nome', 'cpf', 'email', 'senha', 'telefone'],
            $_POST
        );

        if (! empty($erros)) {
            jsonResponse(['erros' => $erros], 400);
        }

        $senhaHash = password_hash($campos['senha'], PASSWORD_DEFAULT);

        try {
            Anunciante::Create(
                $this->pdo,
                $campos['nome'],
                $campos['cpf'],
                $campos['email'],
                $senhaHash,
                $campos['telefone']
            );
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                jsonResponse([
                    'erro' => 'Já existe um anunciante com este CPF ou e-mail.',
                ], 409);
            } else {
                jsonResponse([
                    'erro' => 'Erro no banco de dados: ' . $e->getMessage(),
                ], 500);
            }
        } catch (Exception $e) {
            jsonResponse([
                'erro' => 'Erro inesperado: ' . $e->getMessage(),
            ], 500);
        }

        jsonResponse(['sucesso' => true, 'redirect' => '/login']);
    }

    private function checarLogin(): void
    {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $status = checkUserCredentials($this->pdo, $email, $senha);
        $newLocation = '';

        if ($status === LoginStatus::SUCCESS) {
            $this->startSecureSession();
            $_SESSION['loggedIn'] = true;

            $user = Anunciante::GetSessionUser($this->pdo, $email);

            $_SESSION['user'] = $user;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $newLocation = '/painel';
        }

        jsonResponse(new LoginResult($status, $newLocation));
    }

    private function maisAnuncios(): void
    {
        $filtros = [
            'offset' => $_GET['offset'] ?? 0,
            'marca' => $_GET['marca'] ?? null,
            'modelo' => $_GET['modelo'] ?? null,
            'cidade' => $_GET['cidade'] ?? null,
        ];

        $limite = (int) ($_GET['limit'] ?? 20);

        try {
            [$anuncios, $proxOffset] = Anuncio::GetNFilter($this->pdo, $filtros, $limite);

            foreach ($anuncios as $anuncio) {
                $anuncio->marca = ucwords(htmlspecialchars($anuncio->marca));
                $anuncio->modelo = ucwords(htmlspecialchars($anuncio->modelo));
                $anuncio->ano = htmlspecialchars($anuncio->ano);
                $anuncio->valor = number_format(htmlspecialchars($anuncio->valor), 2, ',', '.');
                $anuncio->cidade = ucwords(htmlspecialchars($anuncio->cidade));
                $anuncio->fotos = array_map('htmlspecialchars', $anuncio->fotos);
            }

            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => true,
                'proxOffset' => $proxOffset,
                'anuncios' => $anuncios,
            ]);
        } catch (Exception $e) {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage(),
            ]);
        }
    }

    private function carregarMarcas(): void
    {
        try {
            $arrayMarcas = Anuncio::GetMarcas($this->pdo);
            array_walk($arrayMarcas, fn ($marca) => $marca = ucwords(htmlspecialchars($marca)));

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($arrayMarcas);
        } catch (Exception $e) {
            exit('Falha inesperada: ' . $e->getMessage());
        }
    }

    private function carregarModelos(): void
    {
        $marca = $_GET['marca'] ?? '';
        try {
            $arrayModelos = Anuncio::GetModelos($this->pdo, $marca);
            array_walk($arrayModelos, fn ($modelo) => $modelo = ucwords(htmlspecialchars($modelo)));

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($arrayModelos);
        } catch (Exception $e) {
            exit('Falha inesperada: ' . $e->getMessage());
        }
    }

    private function carregarCidades(): void
    {
        $filtros = [
            'marca' => $_GET['marca'] ?? null,
            'modelo' => $_GET['modelo'] ?? null,
        ];

        try {
            $arrayCidades = Anuncio::GetCidades($this->pdo, $filtros);
            array_walk($arrayCidades, fn ($cidade) => $cidade = ucwords(htmlspecialchars($cidade)));

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($arrayCidades);
        } catch (Exception $e) {
            exit('Falha inesperada: ' . $e->getMessage());
        }
    }

    private function buscarAnuncio(): void
    {
        $idAnuncio = $_GET['id'];

        try {
            $anuncio = Anuncio::Get($this->pdo, $idAnuncio);

            $anuncio->marca = ucwords(htmlspecialchars($anuncio->marca));
            $anuncio->modelo = ucwords(htmlspecialchars($anuncio->modelo));
            $anuncio->ano = htmlspecialchars($anuncio->ano);
            $anuncio->cor = htmlspecialchars($anuncio->cor);
            $anuncio->descricao = htmlspecialchars($anuncio->descricao);
            $anuncio->estado = htmlspecialchars($anuncio->estado);
            $anuncio->cidade = ucwords(htmlspecialchars($anuncio->cidade));
            $anuncio->fotos = array_map('htmlspecialchars', $anuncio->fotos);
            $anuncio->valor = number_format(htmlspecialchars($anuncio->valor), 2, ',', '.');
            $anuncio->quilometragem = htmlspecialchars($anuncio->quilometragem);

            jsonResponse($anuncio);
        } catch (EntityNotFoundException $e) {
            jsonResponse(['message' => 'Anuncio inexistente.'], 404);
        }
    }

    private function adicionarInteresse(): void
    {
        [$campos, $erros] = validarCamposObrigatorios(
            ['idAnuncio', 'nome', 'telefone', 'mensagem'],
            $_POST
        );

        if (! empty($erros)) {
            jsonResponse(['erros' => $erros], 400);
        }

        try {
            Interesse::Create($this->pdo, $campos['nome'], $campos['telefone'], $campos['mensagem'], $campos['idAnuncio']);
            jsonResponse(['sucesso' => true]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $cookieParams = session_get_cookie_params();
            $cookieParams['httponly'] = true;
            session_set_cookie_params($cookieParams);
            session_start();
        }
    }
}

$acao = $_GET['acao'];

$pdo = mysqlConnect();
$controlador = new Controlador($pdo);
$controlador->handle($acao);
