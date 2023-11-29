<?php
// Substitua pelos valores da sua aplicação
$client_id = "****************************************";
$client_secret = "2PsGupa0WoP7szZfwfMlnJL5E90B9eND";
$redirect_uri = "http://localhost/HEL/index.php";
$code = $_GET['**********************************']; // Captura o código de autorização da URL de redirecionamento

// URL de autenticação da API para obter o token de acesso
$auth_url = "https://api.mercadolibre.com/oauth/token";

// Configura os parâmetros da solicitação POST
$data = array(
    'grant_type' => 'authorization_code',
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'code' => $code
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
?>
