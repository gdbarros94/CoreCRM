document.addEventListener('DOMContentLoaded', function() {
    const nav = document.createElement('nav');
    const ul = document.createElement('ul');

    const pages = [
        { name: 'Visão Geral do Projeto', file: 'overview.html' },
        { name: 'Arquitetura do Sistema', file: 'architecture.html' },
        { name: 'Instalação e Configuração', file: 'installation.html' },
        { name: 'API REST', file: 'api.html' },
        { name: 'Desenvolvimento de Plugins', file: 'plugins.html' },
        { name: 'Contribuindo para o Projeto', file: 'contributing.html' }
    ];

    pages.forEach(page => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = page.file;
        a.textContent = page.name;
        li.appendChild(a);
        ul.appendChild(li);
    });

    nav.appendChild(ul);

    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(nav, container.firstChild);
    }
});

