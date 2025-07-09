<?php
/**
 * Class System
 *
 * Esta classe é responsável pela inicialização e gerenciamento do sistema.
 * Ela inclui funcionalidades para verificar a integridade do sistema, 
 * gerenciar logs e registrar mensagens de log.
 *
 * Métodos principais:
 * - init: Inicializa o sistema e realiza verificações de saúde.
 * - healthCheck: Verifica se o sistema está em condições de funcionar corretamente.
 * - logMessage: Registra uma mensagem em um arquivo de log especificado.
 * - log: Registra uma mensagem no arquivo de log do sistema com um nível de severidade.
 * - getLogs: Retorna os últimos logs para visualização na interface administrativa.
 */

class System
{
    /**
     * Inicializa o sistema.
     *
     * Verifica a integridade do sistema e configura o gerenciamento de logs.
     * Essa função DEVE ser chamada apenas uma vez, na inicialização do sistema.
     *
     * @return void
     */
    public static function init()
    {
        // Health check
        self::healthCheck();

        // Integridade do banco de dados (será implementado com DatabaseHandler)
        // self::checkDatabaseIntegrity();

        // Gerenciamento de logs
        //self::setupLogging();
    }

    /**
     * Verifica a integridade do sistema.
     *
     * Esta função verifica se o sistema está em condições de funcionar corretamente.
     * Implemente aqui verificações de saúde do sistema, como permissões de pasta, extensões PHP necessárias, etc.
     *
     * @return void
     */
    private static function healthCheck()
    {
        // Implementar verificações de saúde do sistema aqui
        // Ex: permissões de pasta, extensões PHP necessárias, etc.
        error_log('System health check completed.');
    }

    /**
     * Registra uma mensagem no arquivo de log especificado.
     *
     * Cria o diretório e o arquivo de log se eles não existirem.
     * Escreve a mensagem no final do arquivo.
     *
     * @param string $logFile Caminho do arquivo de log.
     * @param string $message  Mensagem a ser registrada no log.
     *
     * @throws RuntimeException Se não for possível criar o diretório ou arquivo de log.
     */
    function logMessage($logFile, $message)
    {
        $logDir = dirname($logFile);

        // Verifica se o diretório existe, se não, cria de forma recursiva e segura
        if (!is_dir($logDir)) {
            if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
                throw new RuntimeException("Não foi possível criar o diretório de log: $logDir");
            }
        }

        // Verifica se o arquivo existe, se não, cria
        if (!file_exists($logFile)) {
            $handle = fopen($logFile, 'w');
            if ($handle === false) {
                throw new RuntimeException("Não foi possível criar o arquivo de log: $logFile");
            }
            fclose($handle);
            // Define permissão se necessário
            chmod($logFile, 0644);
        }

        // Escreve a mensagem no log
        file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * Registra uma mensagem no arquivo de log do sistema.
     *
     * O nível de log pode ser especificado como 'info', 'warning' ou 'error'.
     * Se o nível não for especificado, o padrão é 'info'.
     *
     * @param string $message Mensagem a ser registrada no log.
     * @param string $level   Nível do log (info, warning, error).
     *
     * @return void
     */
    public static function log($message, $level = 'info')
    {
        // Centralizar a gestão de logs
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp][$level] $message\n";
        file_put_contents(__DIR__ . '/../logs/system.log', $logEntry, FILE_APPEND);
    }

    /**
     * Retorna os últimos logs para visualização na interface /admin.
     *
     * @param int $limit Quantidade de linhas de log a serem retornadas.
     *                    O padrão é 100.
     *
     * @return array
     */
    public static function getLogs($limit = 100)
    {
        // Retorna os últimos logs para visualização na interface /admin
        $logFile = __DIR__ . '/../logs/system.log';
        if (!file_exists($logFile)) {
            return [];
        }
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_slice($lines, -$limit);
    }
}


