const formulario = document.getElementById('formCadastro');
const caixaErros = document.getElementById('caixaErros');
const campoCusto = document.getElementById('preco_custo');
const campoVenda = document.getElementById('preco_venda');
const campoMargem = document.getElementById('margem');

function calcularMargem() {
    const custo = parseFloat(campoCusto.value);
    const venda = parseFloat(campoVenda.value);

    if (isNaN(custo) || isNaN(venda) || custo <= 0) {
        campoMargem.value = '';
        return;
    }

    const margem = ((venda - custo) / custo) * 100;
    campoMargem.value = margem.toFixed(1) + ' %';
    campoMargem.style.color = margem < 0 ? '#b00020' : '#1b7f3b';
}

campoCusto.addEventListener('input', calcularMargem);
campoVenda.addEventListener('input', calcularMargem);

formulario.addEventListener('submit', function (evento) {
    const erros = [];

    const codigo = document.getElementById('codigo').value.trim();
    const nome = document.getElementById('nome').value.trim();
    const categoria = document.getElementById('categoria').value;
    const custo = parseFloat(campoCusto.value);
    const venda = parseFloat(campoVenda.value);
    const quantidade = document.getElementById('quantidade').value;
    const minimo = document.getElementById('estoque_minimo').value;

    if (codigo === '') {
        erros.push('Informe o código do produto.');
    }
    if (nome.length < 3) {
        erros.push('O nome do produto deve ter pelo menos 3 letras.');
    }
    if (categoria === '') {
        erros.push('Escolha uma categoria.');
    }
    if (isNaN(custo) || custo < 0) {
        erros.push('Preço de custo inválido.');
    }
    if (isNaN(venda) || venda < 0) {
        erros.push('Preço de venda inválido.');
    }
    if (quantidade === '' || !Number.isInteger(Number(quantidade)) || Number(quantidade) < 0) {
        erros.push('A quantidade deve ser um número inteiro maior ou igual a zero.');
    }
    if (minimo === '' || !Number.isInteger(Number(minimo)) || Number(minimo) < 0) {
        erros.push('O estoque mínimo deve ser um número inteiro maior ou igual a zero.');
    }

    if (erros.length > 0) {
        evento.preventDefault();

        caixaErros.innerHTML = '<strong>Corrija os campos abaixo:</strong><ul><li>'
            + erros.join('</li><li>') + '</li></ul>';
        caixaErros.classList.remove('escondido');

        caixaErros.scrollIntoView({ behavior: 'smooth' });
        return;
    }

    if (venda < custo) {
        const continuar = confirm('O preço de venda está MENOR que o custo (prejuízo). Deseja salvar mesmo assim?');
        if (!continuar) {
            evento.preventDefault();
            return;
        }
    }
});

formulario.addEventListener('reset', function () {
    caixaErros.classList.add('escondido');
    campoMargem.value = '';
});
