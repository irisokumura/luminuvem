
let clients = [];
let employees = [];
let currentClientId = null;
let currentCargo = "";
let clientFormReturnPage = "dashboard";

const pages = [
  "loginPage",
  "registerAccountPage",
  "dashboardPage",
  "registerTypePage",
  "formPage",
  "clientsPage",
  "detailPage",
  "employeesPage",
  "employeeFormPage"
];

async function api(url, options = {}) {
  const response = await fetch(url, {
    headers: { "Content-Type": "application/json" },
    credentials: "same-origin",
    ...options
  });

  const data = await response.json().catch(() => ({}));

  if (!response.ok) {
    throw new Error(data.erro || "Ocorreu um erro na comunicação com o servidor.");
  }

  return data;
}

function showError(error) {
  alert(error.message || "Ocorreu um erro.");
}


function showPage(page) {
  if (
    (page === "employees" || page === "employeeForm") &&
    currentCargo !== "gerente"
  ) {
    alert("Apenas gerentes podem acessar esta área.");
    return;
  }

  pages.forEach(id => {
    document.getElementById(id).classList.add("hidden");
  });

  document.getElementById(page + "Page").classList.remove("hidden");

document.querySelector(".topbar").classList.toggle(
  "hidden",
  page === "login" || page === "registerAccount"
);

  if (page === "dashboard") updateDashboard();
  if (page === "clients") loadClients();
  if (page === "employees") loadEmployees();

  if (page === "employeeForm") {
    document.getElementById("employeeForm").reset();
    document.getElementById("employeeMessage").textContent = "";
  }
}


async function loadClients() {
  try {
    clients = await api("clientes.php");
    renderClients();
    updateDashboard();
  } catch (error) {
    showError(error);
  }
}

async function logout() {
  try {
    await api("logout.php", { method: "POST" });
  } catch (error) {
    showError(error);
  }

  currentCargo = "";
  document.getElementById("employeeNavButton").classList.add("hidden");
  document.getElementById("employeeChoice").classList.add("hidden");
  showPage("login");
}

document.getElementById("loginForm").addEventListener("submit", async e => {
  e.preventDefault();

  try {
    const result = await api("login.php", {
      method: "POST",
      body: JSON.stringify({
        email: document.getElementById("loginEmail").value.trim(),
        senha: document.getElementById("loginPassword").value
      })
    });

    currentCargo = result.cargo;

    document.getElementById("employeeNavButton").classList.toggle(
      "hidden",
      currentCargo !== "gerente"
    );

    document.getElementById("employeeChoice").classList.toggle(
    "hidden",
    currentCargo !== "gerente"
    );

    alert("Bem-vindo(a), " + result.nome + "!");

    await loadClients();
    showPage("dashboard");

  } catch (error) {
    showError(error);
  }
});


function newClient() {
  const currentPage = pages.find(id =>
    !document.getElementById(id).classList.contains("hidden")
  );

  clientFormReturnPage = currentPage
    ? currentPage.replace("Page", "")
    : "dashboard";

  clearForm();

  document.getElementById("formTitle").textContent = "Cadastro de Cliente";
  document.getElementById("saveLabel").textContent = "Cadastrar / Salvar";
clientFormReturnPage = "clients";
  showPage("form");
}

function cancelClientForm() {
  clearForm();
  showPage(clientFormReturnPage);
}

function clearForm() {
  document.getElementById("clientForm").reset();
  document.getElementById("clientId").value = "";
}

document.getElementById("clientForm").addEventListener("submit", async e => {
  e.preventDefault();

  const id = document.getElementById("clientId").value;

  const address = [
    value("address"),
    value("city"),
    value("state")
  ].filter(Boolean).join(" - ");

  const data = {
    id_cliente: id ? Number(id) : undefined,
    nome_cliente: value("name"),
    cpf_cliente: value("cpf").replace(/\D/g, ""),
    telefone_cliente: value("phone").replace(/\D/g, ""),
    email_cliente: value("email"),
    endereco_cliente: address
  };

  try {
    await api("clientes.php", {
      method: id ? "PUT" : "POST",
      body: JSON.stringify(data)
    });

    alert(id ? "Cliente atualizado com sucesso!" : "Cliente cadastrado com sucesso!");

    clearForm();
    await loadClients();
    showPage("clients");

  } catch (error) {
    showError(error);
  }
});

// CADASTRO DE CONTA PELA TELA DE LOGIN

document.getElementById("registerAccountForm").addEventListener("submit", async e => {
  e.preventDefault();

  const nome = value("accountName");
  const email = value("accountEmail");
  const senha = document.getElementById("accountPassword").value;
  const confirmarSenha = document.getElementById("accountConfirmPassword").value;

  const mensagem = document.getElementById("accountMessage");
  mensagem.textContent = "";

  if (senha.length < 8) {
    mensagem.textContent = "A senha precisa ter pelo menos 8 caracteres.";
    return;
  }

  if (senha !== confirmarSenha) {
    mensagem.textContent = "As senhas não são iguais.";
    return;
  }

  try {
    const result = await api("cadastrar_conta.php", {
      method: "POST",
      body: JSON.stringify({
        nome: nome,
        email: email,
        senha: senha,
        confirmarSenha: confirmarSenha
      })
    });

    alert(result.mensagem);

    document.getElementById("registerAccountForm").reset();
    mensagem.textContent = "";

    showPage("login");

  } catch (error) {
    mensagem.textContent = error.message;
  }
});

function value(id) {
  return document.getElementById(id).value.trim();
}

function editClient(id) {
  const c = clients.find(item => Number(item.id_cliente) === Number(id));
  if (!c) return;

  currentClientId = id;

  document.getElementById("clientId").value = c.id_cliente;
  document.getElementById("name").value = c.nome_cliente;
  document.getElementById("cpf").value = c.cpf_cliente;
  document.getElementById("phone").value = c.telefone_cliente;
  document.getElementById("email").value = c.email_cliente;
  document.getElementById("address").value = c.endereco_cliente;
  document.getElementById("city").value = "";
  document.getElementById("state").value = "";

  document.getElementById("formTitle").textContent = "Edição de Cliente";
  document.getElementById("saveLabel").textContent = "Salvar alterações";

  showPage("form");
}

function viewClient(id) {
  const c = clients.find(item => Number(item.id_cliente) === Number(id));
  if (!c) return;

  currentClientId = id;

  document.getElementById("clientDetails").innerHTML = `
    <div class="detail-line"><strong>Nome:</strong> ${escapeHTML(c.nome_cliente)}</div>
    <div class="detail-line"><strong>CPF:</strong> ${escapeHTML(c.cpf_cliente)}</div>
    <div class="detail-line"><strong>Contato:</strong> ${escapeHTML(c.telefone_cliente)}</div>
    <div class="detail-line"><strong>E-mail:</strong> ${escapeHTML(c.email_cliente)}</div>
    <div class="detail-line"><strong>Endereço:</strong> ${escapeHTML(c.endereco_cliente)}</div>
    <div class="form-buttons">
      <button class="btn outline" onclick="showPage('clients')">← Voltar</button>
      <button class="btn primary" onclick="editClient(${Number(c.id_cliente)})">✎ Editar</button>
      <button class="btn" style="background:#c34f78;color:white" onclick="deleteClient(${Number(c.id_cliente)})">▤ Excluir</button>
    </div>`;

  showPage("detail");
}

async function deleteClient(id) {
  const c = clients.find(item => Number(item.id_cliente) === Number(id));

  if (!c || !confirm(`Deseja excluir o cadastro de ${c.nome_cliente}?`)) return;

  try {
    await api("clientes.php", {
      method: "DELETE",
      body: JSON.stringify({ id_cliente: Number(id) })
    });

    await loadClients();
    showPage("clients");

  } catch (error) {
    showError(error);
  }
}

function renderClients() {
  const term = (document.getElementById("searchInput")?.value || "").toLowerCase();

  const filtered = clients.filter(c =>
    [c.nome_cliente, c.cpf_cliente, c.email_cliente].some(
      v => (v || "").toLowerCase().includes(term)
    )
  );

  const tbody = document.getElementById("clientsTable");

  tbody.innerHTML = filtered.map(c => `
    <tr>
      <td>${escapeHTML(c.nome_cliente)}</td>
      <td>${escapeHTML(c.cpf_cliente)}</td>
      <td>${escapeHTML(c.email_cliente)}</td>
      <td>${escapeHTML(c.endereco_cliente)}</td>
      <td>
        <div class="actions">
          <button class="mini-btn" onclick="viewClient(${Number(c.id_cliente)})">Ver</button>
          <button class="mini-btn" onclick="editClient(${Number(c.id_cliente)})">Editar</button>
          <button class="mini-btn delete" onclick="deleteClient(${Number(c.id_cliente)})">Excluir</button>
        </div>
      </td>
    </tr>
  `).join("");

  document.getElementById("emptyState").classList.toggle("hidden", filtered.length > 0);

  document.getElementById("clientCount").textContent =
    `${filtered.length} ${filtered.length === 1 ? "cliente" : "clientes"}`;
}

function updateDashboard() {
  document.getElementById("totalClients").textContent = clients.length;
  document.getElementById("summaryTotal").textContent = clients.length;

  document.getElementById("lastDate").textContent =
    clients.length ? "Cadastro mais recente" : "Nenhum cadastro";
}


// LISTAGEM DE FUNCIONÁRIOS

async function loadEmployees() {
  try {
    const result = await api("funcionarios.php");

    employees = Array.isArray(result) ? result : [];

    renderEmployees();
  } catch (error) {
    showError(error);
  }
}

function renderEmployees() {
  const term = (
    document.getElementById("employeeSearch")?.value || ""
  ).toLowerCase();

  const filtered = employees.filter(employee =>
    [
      employee.nome_funcionario,
      employee.email_funcionario,
      employee.cargo
    ].some(value => (value || "").toLowerCase().includes(term))
  );

  const tbody = document.getElementById("employeesTable");

  tbody.innerHTML = filtered.map(employee => `
    <tr>
      <td>${escapeHTML(employee.nome_funcionario)}</td>
      <td>${escapeHTML(employee.email_funcionario)}</td>
      <td>${escapeHTML(employee.cargo)}</td>
    </tr>
  `).join("");

  document.getElementById("employeeCount").textContent =
    `${filtered.length} ${
      filtered.length === 1 ? "funcionário" : "funcionários"
    }`;

  document.getElementById("employeeEmptyState").classList.toggle(
    "hidden",
    filtered.length > 0
  );
}

function newEmployee() {
  showPage("employeeForm");
}


// CADASTRO DE FUNCIONÁRIOS


document.getElementById("employeeForm").addEventListener("submit", async e => {
  e.preventDefault();

  if (currentCargo !== "gerente") {
    alert("Apenas gerentes podem cadastrar funcionários.");
    return;
  }

  const data = {
    nome: value("employeeName"),
    email: value("employeeEmail"),
    senha: document.getElementById("employeePassword").value,
    cargo: value("employeeRole")
  };

  try {
    const result = await api("cadastrar_funcionario.php", {
      method: "POST",
      body: JSON.stringify(data)
    });

    alert(result.mensagem);

    document.getElementById("employeeForm").reset();

    await loadEmployees();
    showPage("employees");

  } catch (error) {
    showError(error);
  }
});


function escapeHTML(text) {
  return String(text ?? "").replace(/[&<>"']/g, char => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;"
  }[char]));
}

document.getElementById("cpf").addEventListener("input", e => {
  e.target.value = e.target.value.replace(/\D/g, "").slice(0, 11);
});

document.getElementById("phone").addEventListener("input", e => {
  e.target.value = e.target.value.replace(/\D/g, "").slice(0, 11);
});

showPage("login");
