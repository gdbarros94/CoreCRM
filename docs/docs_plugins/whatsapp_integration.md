# 📲 Plugin: WhatsApp Integration

**Versão:** 1.0  
**Slug:** `wpp_integration`  
**Descrição:** Plugin para integração básica com o WhatsApp, permitindo o recebimento de mensagens via webhook.  
**Autor:** me

---

## 📦 Definições e Rotas

Este plugin é carregado automaticamente e registra a rota `/webhook` para receber mensagens via POST. O conteúdo recebido é salvo em um arquivo de log para fins de análise e debug.

```php
// main.php
RoutesHandler::addRoute("POST", "/webhook", function() {
    include __DIR__ . '/plugins/whatsapp-integration/webhook.php';
});

## 📁 Estrutura Base

whatsapp-integration/
├── index.php
├── main.php
├── webhook.php
├── plugin.json
└── logs/
    └── webhook.log
