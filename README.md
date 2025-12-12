# Projeto Vitrus

Descrição
---------
Projeto Vitrus é uma aplicação web em PHP (front/back) que contém páginas e scripts para um sistema de dashboard e integrações com banco de dados. O repositório inclui arquivos SQL com esquemas/dumps e código PHP/principal (index.php) para rodar localmente ou em servidor web.

Principais tecnologias
---------------------
- PHP (versão compatível com o código)
- MySQL / MariaDB (para importar os arquivos .sql)
- Servidor web (Apache, Nginx ou similar)
- HTML/CSS/JS para a interface (assets e dashboard)

Estrutura do repositório
------------------------
- assets/ — recursos estáticos (imagens, estilos, scripts)
- dashboard1/ — arquivos relacionados ao dashboard
- php/ — scripts PHP de backend
- index.php — página inicial / ponto de entrada
- assistec.sql — arquivo SQL (possível dump / estrutura de BD)
- curriculo (1).sql — arquivo SQL adicional (possível dump / dados)

Pré-requisitos
--------------
- PHP (recomenda-se PHP 7.4+ ou versão compatível)
- MySQL ou MariaDB
- Servidor web (Apache/Nginx) com suporte a PHP
- Acesso ao terminal para importar SQL (ou ferramenta gráfica como phpMyAdmin)

Instalação e execução
---------------------
1. Clone o repositório:
   git clone https://github.com/CaioMPacheco/Projeto-Vitrus.git
2. Copie os arquivos para a raiz do seu servidor web ou configure um virtual host apontando para a pasta do projeto.
3. Crie um banco de dados no MySQL/MariaDB para o projeto.
4. Importe os arquivos SQL localizados na raiz do repositório:
   - assistec.sql
   - curriculo (1).sql
   (ex.: mysql -u usuario -p nome_do_banco < assistec.sql)
5. Atualize as credenciais de conexão ao banco no(s) arquivo(s) de configuração do projeto (procure por arquivos dentro de php/ ou crie um arquivo de configuração, ex.: php/config.php, com host, usuário, senha e nome do banco).
6. Acesse o projeto no navegador (ex.: http://localhost/Projeto-Vitrus/).

Banco de dados
--------------
- Os arquivos assistec.sql e curriculo (1).sql parecem conter a estrutura e/ou dados necessários. Revise o conteúdo antes de aplicar em um ambiente de produção.
- Faça backup de dados antes de sobrescrever bancos existentes.

Boas práticas e recomendações
----------------------------
- Não deixar credenciais no repositório; use variáveis de ambiente ou um arquivo de configuração ignorado pelo .gitignore.
- Revisar e atualizar permissões de arquivo e diretórios do servidor.
- Sanitizar entradas de usuário e usar prepared statements para evitar SQL injection.

Como contribuir
---------------
1. Abra uma issue descrevendo a sugestão/bug.
2. Crie uma branch nomeada com clareza (ex.: feat/exemplo ou fix/bug-exemplo).
3. Envie um pull request com descrição das mudanças.

Próximos passos sugeridos
-------------------------
- Adicionar um arquivo de configuração de exemplo (ex.: php/config.example.php).
- Incluir instruções de instalação mais detalhadas e requisitos de versão (PHP, extensões).
- Adicionar testes automatizados, se aplicável.
- Inserir um arquivo LICENSE para definir termos de uso.

Contato
-------
- Autor: CaioMPacheco (ver perfil do GitHub)
- Para dúvidas e colaborações, abra uma issue no repositório.

Licença
-------
- Adicione um arquivo LICENSE ao repositório com a licença desejada (ex.: MIT, Apache 2.0) para formalizar o uso do projeto.
