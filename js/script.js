console.log("Sistema M&M carregado com sucesso.");

function mostrarMensagemAgendamento() {
    const mensagem = document.getElementById("mensagemAgendamento");

    if (mensagem) {
        mensagem.innerText = "Formulário preenchido. Em breve entraremos em contato.";
    }
}