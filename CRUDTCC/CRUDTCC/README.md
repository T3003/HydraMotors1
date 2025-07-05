# Apresentação dos Códigos do Projeto CRUDTCC

Este projeto foi desenvolvido para ser um sistema de catálogo de carros, utilizando PHP, MySQL e Bootstrap. A seguir, uma explicação sobre a estrutura dos códigos e o funcionamento das principais partes do sistema.

## Estrutura dos Arquivos

- **config.php**  
  Responsável pela configuração e conexão com o banco de dados MySQL.

- **mysqlexecuta.php**  
  Centraliza a execução de comandos SQL, facilitando a manutenção e reutilização do código.

- **header.php**  
  Contém o cabeçalho e a barra de navegação, garantindo navegação consistente em todas as páginas.

- **categorias/\***  
  Cada arquivo nesta pasta representa uma página de listagem de carros por categoria (Antiguidade, Sedan, SUV, etc).  
  Eles fazem consultas ao banco de dados filtrando os carros pela categoria e exibem os resultados em cards com imagens e nomes. Ao clicar na imagem, o usuário é direcionado para a página de detalhes do carro.

- **Carro.php**  
  Página de exibição detalhada de um carro. Recebe o nome do carro via parâmetro na URL, busca todas as informações no banco e exibe imagens, descrição, história, preço FIPE e outros detalhes.

- **Crud.php**  
  Página de cadastro de novos carros, com formulário para preenchimento de todos os campos necessários, incluindo upload de imagens.

- **Pesquisa.php**  
  Página de resultados de pesquisa, exibindo os carros cujo nome corresponde ao termo pesquisado na barra de busca.

- **style.css**  
  Arquivo de estilos customizados para o layout do sistema.

## Funcionamento Geral

- Todas as páginas que interagem com o banco de dados incluem `config.php` e `mysqlexecuta.php` para garantir conexão e execução segura das queries.
- As páginas de categoria exibem os carros filtrados por categoria, e cada imagem leva à página de detalhes do carro.
- A página de detalhes (`Carro.php`) mostra todas as informações do carro selecionado.
- A barra de pesquisa permite buscar carros por nome, redirecionando para a página `Pesquisa.php` com os resultados.
- O formulário de cadastro permite inserir novos carros no sistema, incluindo upload de imagens e seleção de múltiplas categorias.

## Observação

Os arquivos do projeto possuem comentários explicativos para facilitar o entendimento do fluxo do sistema e das principais funções de cada trecho de código.

---
