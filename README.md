# 📝 Sistema Avançado de Gerenciamento de Notas

Um sistema web robusto desenvolvido em Laravel para organização pessoal e profissional. Criado para otimizar a gestão de tarefas, documentação de rotinas de infraestrutura de TI, acompanhamento de projetos e armazenamento seguro de credenciais.

## ✨ Principais Funcionalidades

### 🔐 Segurança e Privacidade
* **Cofre (Vault):** Notas contendo informações sensíveis (como credenciais de banco de dados, chaves de API e IPs de servidores) podem ser protegidas. O conteúdo é bloqueado na tela inicial e exige autenticação via senha para leitura.
* **Auto-Lock:** O cofre possui um mecanismo de inatividade via JavaScript que oculta os dados sensíveis automaticamente após 15 minutos, removendo a informação do código-fonte (DOM) da página.
* **Links Temporários:** Possibilidade de gerar links públicos para compartilhamento externo com opções de expiração automática (24h ou 7 dias) ou acesso permanente.

### 🚀 Produtividade e Organização
* **Checklists Interativos:** Caixas de seleção renderizadas na página inicial que podem ser marcadas/desmarcadas com um único clique. O status é salvo em background via Fetch API e atualiza uma barra de progresso instantaneamente.
* **Sistema de Prazos (Alertas):** Definição de datas limite para as notas. O sistema alerta visualmente sobre prazos vencidos (borda vermelha e sombreamento) ou com vencimento para o dia atual (ícones em destaque).
* **Drag-and-Drop Inteligente:** Organização visual livre das notas arrastando os cartões pela tela (via SortableJS). O sistema reconhece a área de arrasto e não interfere em cliques no texto ou botões.
* **Live Search e Filtros:** Busca em tempo real no frontend sem recarregar a página e filtragem rápida por categorias/tags através de "pílulas" dinâmicas.

### 🛠️ Edição e Mídia
* **Rich Text Editor:** Integração nativa para formatação completa de texto, suporte a listas, cabeçalhos e tabelas.
* **Highlight.js:** Destaque automático de sintaxe para blocos de código, mantendo a formatação e as cores ideais para leitura de scripts e logs.
* **Upload na Nuvem:** Suporte a upload direto de imagens para a nuvem (AWS S3) em tempo real durante a edição.
* **Exportação para PDF:** Geração automática de arquivos PDF das notas (via DomPDF).
* **Lixeira (Soft Deletes):** Exclusão segura com possibilidade de restauração de notas deletadas acidentalmente.

---

## 💻 Tecnologias Utilizadas

* **Backend:** PHP 8.x / Laravel
* **Banco de Dados:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla / Fetch API)
* **UI/UX:** Bootstrap 5, FontAwesome 6
* **Bibliotecas Adicionais:** Quill.js (Editor), SortableJS (Drag-and-Drop), Highlight.js (Syntax Highlighting), DomPDF (Exportação de PDF).

---

## 🚀 Como Executar o Projeto Localmente

Siga o passo a passo abaixo para configurar o ambiente de desenvolvimento do zero:

### 1. Pré-requisitos
Certifique-se de ter instalado em sua máquina:
* PHP (>= 8.1)
* Composer
* Node.js e NPM
* Servidor de Banco de Dados (MySQL ou MariaDB)

### 2. Clonando o Repositório
Abra o terminal e clone o projeto para o diretório desejado:
```bash
git clone [https://github.com/RichLapu/Projeto-Notas](https://github.com/RichLapu/Projeto-Notas)
cd nome-do-projeto
```

### 3. Instalando Dependências
Instale as dependências do backend (PHP) e do frontend (Node):
```bash
composer install
npm install
```

### 4. Configurando o Ambiente (.env)
Copie o arquivo de exemplo do Laravel para criar o seu arquivo de ambiente local:
```bash
cp .env.example .env
```
Abra o arquivo `.env` gerado e configure as credenciais do seu banco de dados local:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5. Gerando a Chave da Aplicação
Gere a chave de criptografia interna do Laravel:
```bash
php artisan key:generate
```

### 6. Estruturando o Banco de Dados
Execute as migrations para criar todas as tabelas (incluindo as colunas do Cofre e de Lembretes):
```bash
php artisan migrate
```

### 7. Link de Armazenamento Local
Crie o link simbólico para permitir a visualização de arquivos locais (caso não esteja usando o S3 para testes):
```bash
php artisan storage:link
```

### 8. Compilando os Assets do Frontend
Compile os arquivos CSS e JS do projeto (Vite/Mix):
```bash
npm run build
```

### 9. Iniciando o Servidor
Inicie o servidor embutido do PHP:
```bash
php artisan serve
```

🎉 **Pronto!** A aplicação estará disponível no seu navegador no endereço: `http://localhost:8000`.