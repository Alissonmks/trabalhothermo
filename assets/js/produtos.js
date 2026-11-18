const busca = document.querySelector("#buscaProduto");
const categoria = document.querySelector("#filtroCategoria");
const cards = [...document.querySelectorAll(".produto-card")];
function aplicarFiltros() {
const termo = busca.value.toLowerCase().trim();
const filtro = categoria.value;
cards.forEach((card) => {
const nome = card.dataset.nome.toLowerCase();
const cat = card.dataset.categoria;
const combinaNome = nome.includes(termo);
const combinaCategoria = filtro === "todos" || cat === filtro;
card.hidden = !(combinaNome && combinaCategoria);
});
}
busca?.addEventListener("input", aplicarFiltros);
categoria?.addEventListener("change", aplicarFiltros);

const ordenar = document.querySelector("#ordenarProdutos");

const grid = document.querySelector(".produtos-grid");
ordenar?.addEventListener("change", () => {
const ordenados = [...cards];
if (ordenar.value === "menor-preco") {
ordenados.sort((a, b) =>
Number(a.dataset.preco) - Number(b.dataset.preco)
);
}
if (ordenar.value === "maior-preco") {
ordenados.sort((a, b) =>
Number(b.dataset.preco) - Number(a.dataset.preco)
);
}
ordenados.forEach((card) => grid.appendChild(card));
});