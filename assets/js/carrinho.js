let carrinho = [];

const moeda = new Intl.NumberFormat("pt-BR", {
style: "currency",
currency: "BRL"
});
function formatarMoeda(valor) {
return moeda.format(valor);
}
const painel = document.querySelector("#carrinhoPainel");
const overlay = document.querySelector("#carrinhoOverlay");
const abrir = document.querySelector("#abrirCarrinho");
const fechar = document.querySelector("#fecharCarrinho");
const lista = document.querySelector("#carrinhoLista");
const contador = document.querySelector("#contadorCarrinho");
const totalCarrinho = document.querySelector("#totalCarrinho");
const finalizar = document.querySelector("#finalizarPedido");

function abrirCarrinho() {
painel.classList.add("aberto");
overlay.classList.add("aberto");
}
function fecharCarrinho() {
painel.classList.remove("aberto");
overlay.classList.remove("aberto");
}
abrir.addEventListener("click", abrirCarrinho);
fechar.addEventListener("click", fecharCarrinho);
overlay.addEventListener("click", fecharCarrinho);

document.querySelectorAll(".btn-adicionar").forEach((botao) => {
botao.addEventListener("click", () => {
const card = botao.closest(".produto-card");
const produto = {
id: card.dataset.id,
nome: card.dataset.nome,
preco: Number(card.dataset.preco)
};
adicionarProduto(produto);
abrirCarrinho();
});
});

function adicionarProduto(produto) {
const existente = carrinho.find((item) =>
item.id === produto.id
);
if (existente) {
existente.quantidade += 1;
} else {
carrinho.push({
...produto,
quantidade: 1
});
}
atualizarCarrinho();
}

function renderizarCarrinho() {
if (carrinho.length === 0) {
lista.innerHTML = "<p>Seu carrinho esta vazio.</p>";
return;
}
lista.innerHTML = carrinho.map((item) => `
<div class="carrinho-item" data-id="${item.id}">
<div>
<strong>${item.nome}</strong>
<p>${formatarMoeda(item.preco)}</p>
<div class="carrinho-controles">
<button data-acao="menos" type="button">-</button>
<span>${item.quantidade}</span>
<button data-acao="mais" type="button">+</button>
<button data-acao="remover" type="button">
Remover
</button>
</div>
</div>
<strong>
${formatarMoeda(item.preco * item.quantidade)}
</strong>
</div>
`).join("");
}

function atualizarResumo() {
const quantidadeTotal = carrinho.reduce(
(soma, item) => soma + item.quantidade,
0
);
const valorTotal = carrinho.reduce(
(soma, item) => soma + item.preco * item.quantidade,
0
);
contador.textContent = quantidadeTotal;
totalCarrinho.textContent = formatarMoeda(valorTotal);
}

lista.addEventListener("click", (event) => {
const botao = event.target.closest("button[data-acao]");
if (!botao) return;
const itemElemento = botao.closest(".carrinho-item");
const id = itemElemento.dataset.id;
const acao = botao.dataset.acao;
const item = carrinho.find((produto) => produto.id === id);
if (!item) return;
if (acao === "mais") {
item.quantidade += 1;
}
if (acao === "menos") {
item.quantidade -= 1;
}
if (acao === "remover" || item.quantidade <= 0) {
carrinho = carrinho.filter((produto) => produto.id !== id);
}
atualizarCarrinho();
});

function salvarCarrinho() {
localStorage.setItem(
"carrinho-padaria",
JSON.stringify(carrinho)
);
}

function carregarCarrinho() {
const dados = localStorage.getItem("carrinho-padaria");
if (dados) {
carrinho = JSON.parse(dados);
}
}

function atualizarCarrinho() {
renderizarCarrinho();
atualizarResumo();
salvarCarrinho();
}
carregarCarrinho();
atualizarCarrinho();

finalizar.addEventListener("click", () => {
if (carrinho.length === 0) {
alert("Adicione pelo menos um produto.");
return;
}
const valorTotal = carrinho.reduce(
(soma, item) => soma + item.preco * item.quantidade,
0
);
alert(
`Pedido simulado com sucesso!
Total: ${formatarMoeda(valorTotal)}`
);
carrinho = [];
localStorage.removeItem("carrinho-padaria");
atualizarCarrinho();
fecharCarrinho();
});