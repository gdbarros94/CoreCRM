<?php
// token.php - geração de token seguro para API do system_manager

class SystemManagerTokenApi {
    public static function registerRoutes() {
        // POST /api/token
        RoutesHandler::addRoute("POST", "/api/token", [APIHandler::class, 'generateToken']);
    }
}

// Garante que as rotas da API de token sejam registradas ao carregar o módulo
SystemManagerTokenApi::registerRoutes();