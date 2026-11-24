formulario.addEventListener('submit', function (evento) {
const erros = [];
const nome = document.getElementById('nome').value.trim();
if (nome.length < 3) {
erros.push('O nome do produto deve ter pelo menos 3 letras.');
}
// ... outras regras ...
if (erros.length > 0) {
evento.preventDefault(); // CANCELA o envio
caixaErros.innerHTML = erros.join('<br>');
caixaErros.classList.remove('escondido');
}
// sem preventDefault(): o navegador segue e envia para salvar.php
});