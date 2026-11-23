console.log("JavaScript carregado com sucesso!");

const titulo = document.querySelector("h1");
const botao = document.querySelector(".btn-principal");
const cards = document.querySelectorAll(".produto-card");

botao.addEventListener("click", () => {
console.log("O usuario clicou no botao");
});

painel.classList.add("aberto");
painel.classList.remove("aberto");
painel.classList.toggle("aberto");

const menuToggle = document.querySelector("#menuToggle");
const menuPrincipal = document.querySelector("#menuPrincipal");
if (menuToggle && menuPrincipal) {
menuToggle.addEventListener("click", () => {
menuPrincipal.classList.toggle("aberto");
const aberto = menuPrincipal.classList.contains("aberto");
menuToggle.setAttribute("aria-expanded", aberto);
});
}

const botaoTema = document.getElementById('btn-tema');
const htmlElement = document.documentElement;

// 1. Verifica se o usuário já salvou uma preferência antes
const temaSalvo = localStorage.getItem('tema');
if (temaSalvo) {
  htmlElement.setAttribute('data-theme', temaSalvo);
}

// 2. Alterna o tema ao clicar no botão
botaoTema.addEventListener('click', () => {
  const temaAtual = htmlElement.getAttribute('data-theme');
  const novoTema = temaAtual === 'dark' ? 'light' : 'dark';

  htmlElement.setAttribute('data-theme', novoTema);
  localStorage.setItem('tema', novoTema); // Salva a escolha
});


document.addEventListener('DOMContentLoaded', () => {

const inputCPF = document.getElementById('cpf');
const inputTel = document.getElementById('telefone');
const form = document.querySelector('form');
const senha = document.getElementById('senha');
const confirmaSenha = document.getElementById('confirma_senha');

inputCPF?.addEventListener('input', (e) => {
let v = e.target.value.replace(/\D/g, '');
v = v.replace(/(\d{3})(\d)/, '$1.$2')
.replace(/(\d{3})(\d)/, '$1.$2')
.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
e.target.value = v;
});

inputTel?.addEventListener('input', (e) => {
let v = e.target.value.replace(/\D/g, '');
v = v.replace(/^(\d{2})(\d)/g, '($1) $2')
.replace(/(\d{5})(\d)/, '$1-$2');
e.target.value = v;
});

form?.addEventListener('submit', (e) => {

if (senha && confirmaSenha && senha.value !== confirmaSenha.value) {
e.preventDefault();
alert('As senhas não coincidem!');
return;
}

if (window.location.pathname.includes('perfil.php')) {
if (!confirm("Deseja salvar as alterações no telefone?"))

e.preventDefault();

}
});
});