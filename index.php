<?php
// Substitua pelos valores da sua aplicação
$client_id = ""; #Id obtido através da criação da aplicação dentro do Mercado Livre
$client_secret = ""; #Chave obtido através da criação da aplicação dentro do Mercado Livre
$redirect_uri = "http://localhost/HEL/index.php";

// Verifica se o código de autorização foi recebido na URL de redirecionamento
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // URL de autenticação da API para obter o token de acesso
    $auth_url = "https://api.mercadolibre.com/oauth/token";

    // Recupera o code_verifier salvo em uma sessão durante a etapa de autorização
    session_start();
    if (!isset($_SESSION['code_verifier'])) {
        echo "Erro: O code_verifier não foi encontrado na sessão.";
        exit;
    }

    $code_verifier = $_SESSION['code_verifier'];

    // Configura os parâmetros da solicitação POST
    $data = array(
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'code' => $code,
        'code_verifier' => $code_verifier
    );

    // Inicializa a sessão cURL
    $ch = curl_init();

    // Configura as opções da requisição cURL
    curl_setopt($ch, CURLOPT_URL, $auth_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Executa a requisição e obtém a resposta
    $response = curl_exec($ch);

    // Verifica se ocorreu algum erro na requisição
    if (curl_errno($ch)) {
        echo "Erro na requisição: " . curl_error($ch);
        exit;
    }

    // Fecha a sessão cURL
    curl_close($ch);

    // Decodifica a resposta JSON em um array associativo
    $token_info = json_decode($response, true);

    // Verifica se o token de acesso foi obtido com sucesso
    if (isset($token_info['access_token'])) {
        $access_token = $token_info['access_token'];
        echo "Token de Acesso: " . $access_token;
    } else {
        echo "Erro ao obter o Token de Acesso.";
    }
} else {
    // Gera o code_verifier aleatório
    $code_verifier = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

    // Calcula o code_challenge a partir do code_verifier
    $code_challenge = rtrim(strtr(base64_encode(hash('sha256', $code_verifier, true)), '+/', '-_'), '=');

    // Salva o code_verifier na sessão para uso posterior na etapa de obtenção do token
    session_start();
    $_SESSION['code_verifier'] = $code_verifier;

    // URL de autorização com o code_challenge
    $authorize_url = "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id={$client_id}&redirect_uri={$redirect_uri}&code_challenge={$code_challenge}&code_challenge_method=S256";

    // Redireciona o usuário para a página de autorização
    header("Location: {$authorize_url}");
    exit;
}
?>
