const campoSenha = document.querySelector("#senha");
const mostrarSenha = document.querySelector("#mostrarSenha");
if (campoSenha && mostrarSenha) {
mostrarSenha.addEventListener("click", () => {
const estaOculta = campoSenha.type === "password";
campoSenha.type = estaOculta ? "text" : "password";
mostrarSenha.textContent = estaOculta
? "Ocultar senha"
: "Mostrar senha";
});
}

const formLogin = document.querySelector("#formlogin");
if (formLogin) {
formLogin.addEventListener("submit", (event) => {
event.preventDefault();
const email = document.querySelector("#email").value.trim();
const senha = document.querySelector("#senha").value.trim();
if (email === "" || senha === "") {
alert("Preencha e-mail e senha.");
return;
}
alert("Login validado para demonstracao.");
});
}

function mostrarToast(mensagem) {
const toast = document.querySelector("#toast");
if (!toast) return;
toast.textContent = mensagem;
toast.classList.add("visivel");
setTimeout(() => {
toast.classList.remove("visivel");
}, 3000);
}
const formContato = document.querySelector("#formContato");
if (formContato) {
formContato.addEventListener("submit", (event) => {
event.preventDefault();
if (!formContato.checkValidity()) {
formContato.reportValidity();
return;
}
mostrarToast("Mensagem enviada com sucesso!");
formContato.reset();})}

async function simularEnvio(botao) {
botao.disabled = true;
botao.textContent = "Enviando...";
await new Promise((resolve) => setTimeout(resolve, 1200));
botao.disabled = false;
botao.textContent = "Enviar";
}