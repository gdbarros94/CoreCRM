# Contribuindo para o Projeto CoreCRM

O CoreCRM é um projeto open-source e agradecemos qualquer contribuição da comunidade. Seja você um desenvolvedor, um testador ou um escritor de documentação, sua ajuda é valiosa. Este guia descreve como você pode contribuir para o projeto.

## Como Contribuir

Existem várias maneiras de contribuir para o CoreCRM:

1.  **Reportar Bugs**: Se você encontrar um bug, por favor, abra uma issue no repositório do GitHub.
2.  **Sugerir Novas Funcionalidades**: Tem uma ideia para uma nova funcionalidade? Abra uma issue para discutir.
3.  **Contribuir com Código**: Envie pull requests com melhorias, correções de bugs ou novas funcionalidades.
4.  **Melhorar a Documentação**: Ajude a tornar esta documentação ainda melhor, corrigindo erros, adicionando exemplos ou expandindo seções.
5.  **Testar o Software**: Ajude a identificar bugs e garantir a estabilidade do sistema.

## Reportando Bugs e Sugerindo Funcionalidades

Antes de abrir uma nova issue, por favor, verifique se uma issue similar já não existe. Ao abrir uma issue, forneça o máximo de detalhes possível:

*   **Para Bugs**: Descreva os passos para reproduzir o bug, o comportamento esperado e o comportamento observado. Inclua mensagens de erro, logs e informações do ambiente (versão do PHP, servidor web, etc.).
*   **Para Sugestões**: Descreva a funcionalidade que você gostaria de ver, por que ela seria útil e como você a imagina funcionando.

## Contribuindo com Código

Para contribuir com código, siga o fluxo de trabalho padrão do GitHub:

1.  **Faça um Fork do Repositório**: Crie um fork do repositório `gdbarros94/CoreCRM` para sua conta do GitHub.
2.  **Clone seu Fork**: Clone o repositório forkado para sua máquina local:

    ```bash
    git clone https://github.com/SEU_USUARIO/CoreCRM.git
    cd CoreCRM
    ```

3.  **Crie uma Nova Branch**: Crie uma nova branch para suas alterações. Use um nome descritivo para a branch (e.g., `feature/nova-funcionalidade`, `bugfix/corrigir-erro-login`):

    ```bash
    git checkout -b minha-contribuicao
    ```

4.  **Faça suas Alterações**: Implemente suas alterações, correções de bugs ou novas funcionalidades. Certifique-se de seguir as diretrizes de codificação do projeto (se houver).

5.  **Teste suas Alterações**: Antes de enviar, teste suas alterações para garantir que elas funcionam conforme o esperado e não introduzem novos bugs.

6.  **Commit suas Alterações**: Faça commits claros e concisos. Use mensagens de commit que descrevam o que foi alterado e por quê:

    ```bash
    git add .
    git commit -m "feat: Adiciona nova funcionalidade X"
    ```

7.  **Envie para seu Fork**: Envie suas alterações para seu repositório forkado no GitHub:

    ```bash
    git push origin minha-contribuicao
    ```

8.  **Abra um Pull Request**: Vá para a página do seu repositório forkado no GitHub e abra um Pull Request para a branch `main` do repositório original `gdbarros94/CoreCRM`. Descreva suas alterações em detalhes e referencie quaisquer issues relacionadas.

## Diretrizes de Codificação

*   **Padrões de Código**: Tente seguir os padrões de código existentes no projeto. Se não houver padrões formais, use um estilo consistente.
*   **Comentários**: Comente seu código onde for necessário para explicar a lógica complexa ou decisões de design.
*   **Testes**: Se possível, inclua testes unitários ou de integração para suas alterações.

## Licença

Ao contribuir com o CoreCRM, você concorda que suas contribuições serão licenciadas sob a mesma licença do projeto (MIT License).

