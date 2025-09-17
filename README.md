# Portal de Anúncios de Veículos

Projeto desenvolvido como trabalho final da disciplina **Programação para Internet (PPI)**, ofertada na Faculdade de Computação da **Universidade Federal de Uberlândia (UFU)**.

O sistema consiste em um portal web para **anúncios de veículos**, permitindo que usuários possam se cadastrar, efetuar login, publicar anúncios e gerenciar mensagens de interesse de visitantes.

---

## Funcionalidades

### Área pública

* Página principal com listagem dos últimos anúncios cadastrados (formato *cards*).
* Filtros dinâmicos de busca por **marca, modelo e cidade**.
* Visualização detalhada de anúncios.
* Registro de mensagens de interesse em veículos (nome, telefone e mensagem).
* Páginas de **cadastro** e **login** de usuários.

### Área restrita (usuário logado)

* Criação de novos anúncios com upload de imagens (mínimo de 3 fotos).
* Listagem e gerenciamento dos anúncios do usuário.
* Visualização detalhada dos próprios anúncios.
* Recebimento e exclusão de mensagens de interesse.
* Exclusão de anúncios (incluindo fotos associadas).
* Opção de logoff.

---

## Tecnologias Utilizadas

* **Front-end**:

  * HTML5
  * CSS3 (Flexbox, estilização manual)
  * JavaScript (manipulação de DOM, Fetch API/Ajax)
  * Bootstrap (opcional na área restrita)

* **Back-end**:

  * PHP com **PDO** para comunicação com o banco
  * Arquitetura **MVC**
  * Controle de sessões

* **Banco de Dados**:

  * MySQL
  * Transações para consistência de dados
  * Armazenamento seguro de senhas (hash)

---

## Requisitos e Boas Práticas

* Layout responsivo em todas as páginas.
* Validação do HTML e CSS nos validadores oficiais (W3C).
* Prevenção contra **SQL Injection** e **XSS**.
* Respostas do servidor sempre em **JSON**, manipuladas dinamicamente no front-end.
* Hospedagem em serviços gratuitos como InfinityFree ou AwardSpace.

---

## Estrutura do Projeto

```
/raiz-do-projeto
│── /css              # Arquivos de estilo
│── /js               # Scripts JavaScript
│── /img              # Logotipo e imagens fixas
│── /uploads          # Fotos usadas como logo e placeholder
│── /php              # Arquivos em PHP
│── ...               # Páginas HTML/PHP (MVC)
```

---

## Equipe

Projeto realizado por estudantes da **Faculdade de Computação - UFU**, sob orientação do Prof. Daniel A. Furtado.
- Enzo Lazzarini Amorim
- João Lucas Pontes Freitas

---

## Licença

Uso acadêmico e educacional.
