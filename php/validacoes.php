<?php

function toString($campo)
{
    if ($campo === 'email') {
        $serialized = 'E-mail';
    } elseif ($campo === 'cpf') {
        $serialized = 'CPF';
    } else {
        $serialized = ucfirst($campo);
    }

    return $serialized;
}

function validarCamposObrigatorios($camposObrigatorios, $postData)
{
    $camposNumericos = ['quilometragem', 'ano', 'valor'];
    $campos = [];
    $erros = [];

    foreach ($camposObrigatorios as $campo) {
        if (in_array($campo, $camposNumericos)) {
            $valor = $postData[$campo];

            if (! is_numeric($valor)) {
                $erros[$campo] = toString($campo) . ' deve ser preenchido com um número.';
            }
        } else {
            $valor = trim($postData[$campo] ?? '');

            if ($valor === '') {
                $erros[$campo] = toString($campo) . ' é obrigatório.';
            }
        }

        $campos[$campo] = $valor;
    }

    return [$campos, $erros];
}

function validaFoto($arquivoImagem)
{
    if (! is_uploaded_file($arquivoImagem)) {
        throw new InvalidArgumentException('Falha ao carregar o arquivo de imagem.');
    }

    [$width, $height, $type] = getimagesize($arquivoImagem);
    if (empty($width) || empty($height)) {
        throw new InvalidArgumentException('O arquivo informado não é uma imagem válida.');
    }

    $imageType = image_type_to_mime_type($type);
    if (! in_array($imageType, ['image/jpeg', 'image/webp', 'image/png'])) {
        throw new InvalidArgumentException('A foto deve estar no formato PNG, JPG (ou JPEG) ou WEBP.');
    }

    if (filesize($arquivoImagem) > 10 * 1024 * 1024) {
        throw new InvalidArgumentException('A foto não deve ultrapassar 10MB.');
    }

    return $imageType;
}

function validaFotos($fotos, $pasta = 'images/anuncios')
{
    // Caminho absoluto no servidor para salvar os arquivos
    $pastaServidor = $_SERVER['DOCUMENT_ROOT'] . '/' . $pasta;
    if (! is_dir($pastaServidor)) {
        mkdir($pastaServidor, 0777, true);
    }

    // URL base do site
    $protocolo = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $rootUrl = $protocolo . '://' . $host;

    $salvos = [];

    foreach ($fotos['tmp_name'] as $index => $tmpName) {
        try {
            $tipoArquivoImagem = validaFoto($tmpName);

            // hash único do conteúdo
            $hash = hash_file('sha256', $tmpName);

            // extensão a partir do MIME
            $extensao = substr($tipoArquivoImagem, 6);

            // caminho absoluto no servidor
            $destinoArquivo = $pastaServidor . "/{$hash}.{$extensao}";

            if (! file_exists($destinoArquivo)) {
                if (! move_uploaded_file($tmpName, $destinoArquivo)) {
                    throw new RuntimeException("Erro ao mover o arquivo {$fotos['name'][$index]}");
                }
            }

            // adiciona URL completa para salvar no banco
            $urlArquivo = $rootUrl . '/' . $pasta . "/{$hash}.{$extensao}";
            $salvos[] = $urlArquivo;

        } catch (Exception $e) {
            // remove arquivos já salvos em caso de erro
            foreach ($salvos as $arquivo) {
                $caminhoServidor = $_SERVER['DOCUMENT_ROOT'] . parse_url($arquivo, PHP_URL_PATH);
                if (file_exists($caminhoServidor)) {
                    unlink($caminhoServidor);
                }
            }
            throw new RuntimeException("Falha na foto {$fotos['name'][$index]}: " . $e->getMessage());
        }
    }

    return $salvos; // array de URLs completas
}

// Achei no github:
// https://gist.github.com/rafael-neri/ab3e58803a08cb4def059fce4e3c0e40
function validaCPF($cpf)
{

    // Extrai somente os números
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);

    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }

    return true;
}
