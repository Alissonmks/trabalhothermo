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

const formlogin=document.querySelector("#formlogin");
if(formlogin){
    formlogin.addEventListener("submit" , (event)=>{
        const email=document.querySelector("#email").value.trim();
        const senha=document.querySelector("#senha").value.trim();
      if(email ==="" || senha===""){
        event.preventDefault();
        alert("preencha email e senha.");
        return;
    
    }
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

document.addEventListener('DOMContentLoaded', () => {
    const inputCPF = document.getElementById('cpf');
    const inputTel = document.getElementById('telefone');
    const form = document.querySelector('form');
    const senha = document.getElementById('senha');
    const confirmaSenha = document.getElementById('confirma_senha');

    inputCPF?.addEventListener('input', (e) => {
        let v = e.target.value.replace(/\D/g, '');
        v = v.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = v;
    });

    inputTel?.addEventListener('input', (e) => {
        let v = e.target.value.replace(/\D/g, '');
        v = v.replace(/^(\d{2})(\d)/g, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = v;
    });

    form?.addEventListener('submit', (e) => {
        if (senha && confirmaSenha && senha.value !== confirmaSenha.value) {
            e.preventDefault();
            alert('As senhas não coincidem!');
            return;
        }

        if (window.location.pathname.includes('perfil.php')) {
            if (!confirm("Deseja salvar as alterações no telefone?")) e.preventDefault();
        }
    });
});