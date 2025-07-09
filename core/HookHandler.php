<?php

/**
 * Classe HookHandler
 *
 * Esta classe gerencia a implementação de hooks (ganchos) que permitem a execução de funções de callback
 * em momentos específicos durante a execução de ações. Os hooks podem ser registrados para serem executados
 * antes ou depois de uma ação específica, permitindo a extensão e personalização do comportamento do sistema.
 *
 * Exemplos de uso incluem a adição de funcionalidades extras antes ou depois de gerar relatórios, processar
 * dados, ou qualquer outra ação que possa ser estendida.
 *
 * Métodos:
 * - register_hook: Registra um hook para uma ação específica.
 * - do_action: Executa uma ação, acionando os hooks 'before' e 'after' associados.
 */

class HookHandler
{
    private static $hooks = [];

    /**
     * Registra um hook (função de callback) para uma ação específica.
     *
     * @param string $actionName Nome da ação (ex: 'gerar_relatorio').
     * @param callback $callback Função a ser executada quando o hook for acionado.
     * @param string $when Quando o hook deve ser executado ('before' ou 'after').
     * @param int $priority Prioridade de execução (menor número executa primeiro).
     */
    public static function register_hook($actionName, $callback, $when = 'after', $priority = 10)
    {
        if (!in_array($when, ['before', 'after'])) {
            System::log("Invalid 'when' parameter for hook: {$actionName}. Must be 'before' or 'after'.", "error");
            return;
        }

        if (!is_callable($callback)) {
            System::log("Invalid 'callback' for hook: {$actionName}. Must be a callable function.", "error");
            return;
        }

        self::$hooks[$actionName][$when][$priority][] = $callback;
        System::log("Hook registered: {$actionName} ({$when}, priority {$priority})");
    }

    /**
     * Executa uma ação, acionando os hooks 'before' e 'after' associados.
     *
     * @param string $actionName Nome da ação a ser executada.
     * @param array $args Argumentos a serem passados para a função da ação e para os hooks.
     * @param callable|null $actionCallback A função real da ação a ser executada. Se nulo, apenas os hooks são executados.
     * @return mixed O resultado da função da ação, ou null se não houver função de ação.
     */
    public static function do_action($actionName, $args = [], $actionCallback = null)
    {
        System::log("Executing action: {$actionName}", "debug");
        $result = null;

        // Executa hooks 'before'
        if (isset(self::$hooks[$actionName]['before'])) {
            ksort(self::$hooks[$actionName]['before']); // Ordena por prioridade
            foreach (self::$hooks[$actionName]['before'] as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    System::log("Executing 'before' hook for {$actionName} (priority {$priority})", "debug");
                    call_user_func_array($callback, $args);
                }
            }
        }

        // Executa a função real da ação, se fornecida
        if (is_callable($actionCallback)) {
            System::log("Executing main action callback for {$actionName}", "debug");
            $result = call_user_func_array($actionCallback, $args);
        }

        // Executa hooks 'after'
        if (isset(self::$hooks[$actionName]['after'])) {
            ksort(self::$hooks[$actionName]['after']); // Ordena por prioridade
            foreach (self::$hooks[$actionName]['after'] as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    System::log("Executing 'after' hook for {$actionName} (priority {$priority})", "debug");
                    call_user_func_array($callback, $args);
                }
            }
        }

        return $result;
    }
}


