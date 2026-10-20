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

const temaSalvo = localStorage.getItem("tema");
if (temaSalvo === "escuro") {
document.body.classList.add("tema-escuro");
}
botaoTema?.addEventListener("click", () => {
document.body.classList.toggle("tema-escuro");
const escuro = document.body.classList.contains("tema-escuro");
localStorage.setItem("tema", escuro ? "escuro" : "claro");
});

