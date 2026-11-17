<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Luminuvem - Sistema de Clientes</title>
  <link rel="stylesheet" href="style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div id="app">
    <header class="topbar">
<a class="brand" href="#" onclick="showPage('dashboard'); return false;">
  <img
    src="assets/logo-luminuvem.png"
    alt="Logo Luminuvem"
    class="brand-logo"
  >
  <span>Luminuvem</span>
</a>
      <nav class="topnav">
        <button onclick="showPage('dashboard')">◷ Dashboard</button>
        <button onclick="showPage('registerType')">＋ Cadastrar</button>        <button onclick="showPage('clients')">☷ Clientes</button>
        <button id="employeeNavButton" class="hidden" onclick="showPage('employees')">
         ♟ Funcionários
         </button>
        <button class="logout" onclick="logout()">⇥ Sair</button>
      </nav>
    </header>

    <main>
      <section id="loginPage" class="login-layout">
        <div class="login-card">
<img
  src="assets/logo-luminuvem.png"
  alt="Logo Luminuvem"
  class="login-logo"
>          <h1>Acesso ao sistema</h1>
          <p class="muted">Entre para gerenciar seus clientes</p>
          <form id="loginForm">
            <label for="loginEmail">E-mail</label>
            <input id="loginEmail" type="email" placeholder="Digite seu e-mail" required>
            <label for="loginPassword">Senha</label>
            <input id="loginPassword" type="password" placeholder="Digite sua senha" required>
            <div class="login-actions">
              <button class="btn primary" type="submit">⇥ Entrar</button>
              <button class="text-button" type="button" onclick="alert('Entre em contato com o administrador para recuperar sua senha.')">Esqueceu sua senha?</button>
            </div>
          </form>

          <small class="muted">
            Não tem conta?
            <a href="#" onclick="showPage('registerAccount'); return false;">
              Cadastre-se aqui
            </a>
          </small>        
        </div>
      </section>

 <section id="registerAccountPage" class="login-layout hidden">
  <div class="login-card">

    <img
      src="assets/logo-luminuvem.png"
      alt="Logo Luminuvem"
      class="login-logo"
    >

          <h1>Criar minha conta</h1>
          <p class="muted">
            Preencha seus dados para acessar o Luminuvem
          </p>

          <form id="registerAccountForm">

            <label for="accountName">Nome completo</label>
            <input
              id="accountName"
              type="text"
              placeholder="Digite seu nome completo"
              required
            >

            <label for="accountEmail">E-mail</label>
            <input
              id="accountEmail"
              type="email"
              placeholder="Digite seu e-mail"
              required
            >

            <label for="accountPassword">Senha</label>
            <input
              id="accountPassword"
              type="password"
              minlength="8"
              placeholder="Mínimo de 8 caracteres"
              required
            >

            <label for="accountConfirmPassword">
              Confirmar senha
            </label>
            <input
              id="accountConfirmPassword"
              type="password"
              minlength="8"
              placeholder="Digite a senha novamente"
              required
            >

            <div class="login-actions">
              <button class="btn primary" type="submit">
                Criar conta
              </button>

              <button
                class="text-button"
                type="button"
                onclick="showPage('login')"
              >
                Voltar para o login
              </button>
            </div>

            <p id="accountMessage" class="muted"></p>

          </form>
        </div>
      </section>

      <section id="dashboardPage" class="page hidden">
        <div class="page-heading"><span class="heading-icon">▦</span><div><h1>Dashboard</h1><p class="muted">Visão geral do seu sistema</p></div></div>
        <div class="dashboard-grid">
          <article class="stat-card">
            <div class="stat-icon">♟</div>
            <div class="stat-number" id="totalClients">0</div>
            <div class="muted">Total de clientes</div>
          </article>
          <article class="stat-card shortcuts">
            <div class="stat-icon">▥</div>
            <h3>Atalhos</h3>
            <button class="shortcut" onclick="newClient()">Cadastrar cliente <span>›</span></button>
            <button class="shortcut" onclick="showPage('clients')">Consultar clientes <span>›</span></button>
          </article>
          <article class="summary-card">
            <h3>Resumo</h3>
            <div class="summary-row"><span class="summary-icon">♟</span><div><small>Total de clientes cadastrados</small><strong id="summaryTotal">0</strong></div></div>
            <div class="summary-row"><span class="summary-icon">▦</span><div><small>Último cadastro</small><strong id="lastDate">Nenhum cadastro</strong></div></div>
          </article>
          <article class="quick-card"><h3>Acesso rápido</h3><p class="muted">Utilize os atalhos para administrar seus registros.</p><button class="btn outline" onclick="newClient()">＋ Novo cliente</button></article>
        </div>
      </section>

<section id="registerTypePage" class="page hidden">

  <div class="page-heading">
    <span class="heading-icon">＋</span>
    <div>
      <h1>Central de Cadastros</h1>
      <p class="muted">Escolha o tipo de cadastro que deseja realizar</p>
    </div>
  </div>

  <div class="registration-options">

    <article class="registration-card">
      <div class="registration-icon">♙</div>
      <h2>Cadastro de Cliente</h2>
      <p class="muted">
        Cadastre novos clientes e registre suas informações
        para manter tudo organizado.
      </p>
      <button class="btn primary" type="button"
       onclick="newClient()">
       ＋ Cadastrar cliente
      </button>
    </article>

    <article class="registration-card" id="employeeChoice">
      <div class="registration-icon">♟</div>
      <h2>Cadastro de Funcionário</h2>
      <p class="muted">
        Adicione novos integrantes à equipe Luminuvem
        e defina seus respectivos cargos.
      </p>
      <button class="btn primary" type="button"
        onclick="showPage('employeeForm')">
        ＋ Cadastrar funcionário
      </button>
      <small class="muted">Acesso exclusivo para gerentes</small>
    </article>

  </div>

</section>

      <section id="formPage" class="page hidden">
        <div class="page-heading"><span class="heading-icon">✎</span><div><h1 id="formTitle">Cadastro de Cliente</h1><p class="muted">Preencha os dados abaixo</p></div></div>
        <form id="clientForm" class="panel">
          <input type="hidden" id="clientId">
          <div class="form-grid">
            <div class="field full"><label for="name">Nome completo</label><input id="name" required placeholder="Digite o nome completo"></div>
            <div class="field"><label for="cpf">CPF</label><input id="cpf" required maxlength="14" placeholder="000.000.000-00"></div>
            <div class="field"><label for="phone">Telefone</label><input id="phone" required placeholder="(00) 00000-0000"></div>
            <div class="field"><label for="email">E-mail</label><input id="email" type="email" required placeholder="exemplo@email.com"></div>
            <div class="field full"><label for="address">Endereço</label><input id="address" required placeholder="Rua, número, complemento"></div>
            <div class="field"><label for="city">Cidade</label><input id="city" required placeholder="Digite a cidade"></div>
            <div class="field"><label for="state">Estado</label>
              <select id="state" required>
                <option value="">Selecione o estado</option>
                <option>AC</option><option>AL</option><option>AP</option><option>AM</option><option>BA</option><option>CE</option><option>DF</option><option>ES</option><option>GO</option><option>MA</option><option>MT</option><option>MS</option><option>MG</option><option>PA</option><option>PB</option><option>PR</option><option>PE</option><option>PI</option><option>RJ</option><option>RN</option><option>RS</option><option>RO</option><option>RR</option><option>SC</option><option>SP</option><option>SE</option><option>TO</option>
              </select>
            </div>
          </div>
          <div class="form-buttons">
            <button class="btn primary" type="submit">▣ <span id="saveLabel">Cadastrar / Salvar</span></button>
            <button class="btn outline" type="reset" onclick="clearForm()">↻ Limpar</button>
<button
  class="btn outline"
  type="button"
  onclick="cancelClientForm()"
>
  ← Voltar
</button>        </form>
      </section>

      <section id="clientsPage" class="page hidden">
        <div class="page-heading heading-between"><div class="page-heading"><span class="heading-icon">☷</span><div><h1>Clientes cadastrados</h1><p class="muted">Consulte e gerencie seus clientes</p></div></div><button class="btn primary" onclick="newClient()">＋ Novo cliente</button></div>
        <div class="panel">
          <div class="search-row"><input id="searchInput" placeholder="Buscar por nome, CPF ou e-mail..." oninput="renderClients()"><span class="muted" id="clientCount">0 clientes</span></div>
          <div class="table-wrap">
            <table><thead><tr><th>Nome</th><th>CPF</th><th>E-mail</th><th>Cidade</th><th>Ações</th></tr></thead><tbody id="clientsTable"></tbody></table>
          </div>
          <p id="emptyState" class="empty muted hidden">Nenhum cliente encontrado. Cadastre seu primeiro cliente.</p>
        </div>
      </section>


<section id="employeesPage" class="page hidden">

  <div class="page-heading heading-between">
    <div class="page-heading">
      <span class="heading-icon">♟</span>
      <div>
        <h1>Funcionários cadastrados</h1>
        <p class="muted">Consulte e gerencie os integrantes da equipe</p>
      </div>
    </div>

    <button class="btn primary" onclick="newEmployee()">
      ＋ Novo funcionário
    </button>
  </div>

  <div class="panel">

    <div class="search-row">
      <input
        id="employeeSearch"
        placeholder="Buscar por nome, e-mail ou cargo..."
        oninput="renderEmployees()"
      >

      <span class="muted" id="employeeCount">
        0 funcionários
      </span>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Cargo</th>
          </tr>
        </thead>

        <tbody id="employeesTable"></tbody>
      </table>
    </div>

    <p id="employeeEmptyState" class="empty muted hidden">
      Nenhum funcionário encontrado.
    </p>

  </div>

</section>

<!-- FORMULÁRIO DE CADASTRO DE FUNCIONÁRIO -->
<section id="employeeFormPage" class="page hidden">

  <div class="page-heading">
    <span class="heading-icon">＋</span>
    <div>
      <h1>Novo Funcionário</h1>
      <p class="muted">Preencha os dados do novo integrante da equipe</p>
    </div>
  </div>

  <form id="employeeForm" class="panel">

    <div class="form-grid">

      <div class="field full">
        <label for="employeeName">Nome completo</label>
        <input
          id="employeeName"
          type="text"
          placeholder="Digite o nome completo"
          required
        >
      </div>

      <div class="field full">
        <label for="employeeEmail">E-mail</label>
        <input
          id="employeeEmail"
          type="email"
          placeholder="exemplo@email.com"
          required
        >
      </div>

      <div class="field">
        <label for="employeePassword">Senha</label>
        <input
          id="employeePassword"
          type="password"
          minlength="8"
          placeholder="Mínimo de 8 caracteres"
          required
        >
      </div>

      <div class="field">
        <label for="employeeRole">Cargo</label>
        <select id="employeeRole" required>
          <option value="">Selecione o cargo</option>
          <option value="gerente">Gerente</option>
          <option value="funcionario">Funcionário</option>
        </select>
      </div>

    </div>

    <div class="form-buttons">

      <button class="btn primary" type="submit">
        ＋ Cadastrar funcionário
      </button>

      <button class="btn outline" type="reset">
        ↻ Limpar
      </button>

      <button
        class="btn outline"
        type="button"
        onclick="showPage('employees')"
      >
        ← Voltar para funcionários
      </button>

    </div>

    <p id="employeeMessage" class="muted"></p>

  </form>

</section>

      <section id="detailPage" class="page hidden">
        <div class="page-heading"><span class="heading-icon">♙</span><div><h1>Visualização do Cliente</h1><p class="muted">Informações completas do cadastro</p></div></div>
        <div class="panel detail-panel" id="clientDetails"></div>
      </section>
    </main>
    <footer> Sistema de Clientes <span>•</span> Projeto demonstrativo</footer>
  </div>
  <script src="script.js"></script>
</body>
</html>
