
<?php

// Este arquivo é o ponto de entrada principal para o seu plugin.
// Ele é carregado pelo PluginHandler quando o plugin está ativo.

// Exemplo de registro de uma rota para o plugin
RoutesHandler::addRoute("GET", "/exemplo-plugin/teste", function() {
    echo "Esta é uma página de teste do Exemplo de Plugin!";
});

// Exemplo de registro de um hook
HookHandler::register_hook("after_gerar_relatorio", function() {
    System::log("Hook do plugin executado após gerar relatório.", "info");
});

System::log("Plugin Exemplo de Plugin carregado com sucesso.");



