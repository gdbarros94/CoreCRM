
# Visão Geral do Projeto CoreCRM

O CoreCRM é um sistema de Customer Relationship Management (CRM) modular e flexível, desenvolvido em PHP puro. Sua principal característica é a arquitetura baseada em plugins, que permite uma alta capacidade de extensão e personalização, adaptando-se a diversas necessidades de negócio. O projeto foi concebido com o objetivo de ser uma plataforma robusta e escalável para o gerenciamento de relacionamentos com clientes.

## Propósito e Filosofia

O CoreCRM foi criado para oferecer uma solução de CRM que combina simplicidade no desenvolvimento com poderosas funcionalidades de extensão. Inspirado em frameworks modernos e sistemas como o WordPress, ele adota um modelo de 


plugins e hooks, permitindo que desenvolvedores adicionem novas funcionalidades ou modifiquem as existentes sem alterar o código-fonte principal. Isso garante a facilidade de manutenção, atualização e colaboração no projeto.

## Principais Funcionalidades

*   **Modularidade via Plugins**: Permite o carregamento dinâmico de funcionalidades e módulos, tornando o sistema altamente adaptável.
*   **Sistema de Temas Desacoplado**: A interface do usuário pode ser facilmente personalizada através de temas, sem afetar a lógica de negócio.
*   **Roteamento Dinâmico**: Gerenciamento flexível de URLs e requisições através do `RoutesHandler`.
*   **Autenticação e Controle de Acesso (ACL)**: Sistema robusto para gerenciamento de usuários, sessões e permissões.
*   **API REST Completa**: Facilita a integração com outras aplicações e serviços externos, permitindo a comunicação programática com o CoreCRM.
*   **Sistema de Hooks e Actions**: Um mecanismo poderoso para estender o sistema, permitindo que desenvolvedores 'engatem' suas funções em pontos específicos da execução do CoreCRM.
*   **Interface Administrativa**: Um painel de controle intuitivo para gerenciar configurações, usuários, plugins e outros aspectos do sistema.
*   **Instalação de Plugins via Upload**: Simplifica o processo de adição de novas funcionalidades ao sistema.
*   **Query Builder Integrado**: Facilita a interação com o banco de dados de forma segura e eficiente.
*   **Configuração Global Flexível**: Permite ajustar o comportamento do sistema através de um arquivo de configuração centralizado.

## Público-Alvo

Esta documentação é destinada a:

*   **Desenvolvedores**: Que desejam estender o CoreCRM, criar novos plugins, integrar com outras aplicações ou contribuir para o código-fonte.
*   **Administradores de Sistema**: Que precisam instalar, configurar e manter o CoreCRM em seus ambientes.
*   **Usuários Avançados**: Que buscam entender o funcionamento interno do sistema para otimizar seu uso.

Ao longo desta documentação, você encontrará informações detalhadas sobre cada componente, exemplos de código e melhores práticas para trabalhar com o CoreCRM.

