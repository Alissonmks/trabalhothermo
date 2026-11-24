const formFiltro = document.getElementById('formFiltro');
const btnLimpar = document.getElementById('btnLimpar');

btnLimpar.addEventListener('click', function () {
    window.location.href = 'consulta.php';
});

formFiltro.addEventListener('submit', function (evento) {
    const min = parseFloat(document.getElementById('preco_min').value);
    const max = parseFloat(document.getElementById('preco_max').value);

    if (!isNaN(min) && !isNaN(max) && min > max) {
        evento.preventDefault();
        alert('O preço "de" não pode ser maior que o preço "até".');
    }
});
