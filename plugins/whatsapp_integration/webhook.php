<?php
// Define o caminho do arquivo de log
$logFile = __DIR__ . '/logs/webhook.log';

// Verifica se há dados recebidos via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém o payload recebido
    $payload = file_get_contents('php://input');

    // Salva o payload no arquivo de log
    file_put_contents($logFile, $payload . PHP_EOL, FILE_APPEND);

    // Retorna uma resposta de sucesso
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    // Retorna erro para métodos diferentes de POST
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}