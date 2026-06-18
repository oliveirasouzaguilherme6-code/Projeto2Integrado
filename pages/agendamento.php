<?php
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"] ?? "";
    $telefone = $_POST["telefone"] ?? "";
    $veiculo = $_POST["veiculo"] ?? "";
    $servico = $_POST["servico"] ?? "";
    $descricao = $_POST["descricao"] ?? "";

    if ($nome == "" || $telefone == "" || $veiculo == "" || $servico == "") {
        $mensagem = "Preencha os campos obrigatórios.";
    } else {
        $mensagem = "Solicitação enviada com sucesso. Em breve entraremos em contato.";
    }
}
?>

<section class="section page-top">
    <div class="container">

        <div class="section-title center">
            <span>Agendamento</span>
            <h1>Solicite uma avaliação</h1>
            <p>
                Informe os dados do veículo e o serviço desejado para organizar o atendimento.
            </p>
        </div>

        <div class="row g-5">

            <div class="col-lg-5">
                <div class="info-side">
                    <h3>Atendimento M&M</h3>

                    <p>
                        A solicitação ajuda a entender melhor o serviço antes do orçamento.
                    </p>

                    <div class="clean-list">
                        <div>Estética automotiva</div>
                        <div>Funilaria e pintura</div>
                        <div>Troca de peças</div>
                        <div>Orçamento sob avaliação</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="form-card">

                    <?php if ($mensagem != ""): ?>
                        <div class="message-box">
                            <?php echo $mensagem; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label>Nome completo *</label>
                                <input type="text" name="nome" class="form-control" placeholder="Digite seu nome">
                            </div>

                            <div class="col-md-6">
                                <label>Telefone / WhatsApp *</label>
                                <input type="text" name="telefone" class="form-control" placeholder="(44) 99999-9999">
                            </div>

                            <div class="col-md-6">
                                <label>Veículo *</label>
                                <input type="text" name="veiculo" class="form-control" placeholder="Ex: Corolla 2018">
                            </div>

                            <div class="col-md-6">
                                <label>Serviço desejado *</label>
                                <select name="servico" class="form-select">
                                    <option value="">Selecione</option>
                                    <option value="Estética automotiva">Estética automotiva</option>
                                    <option value="Funilaria">Funilaria</option>
                                    <option value="Pintura">Pintura</option>
                                    <option value="Troca de peças">Troca de peças</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label>Descrição</label>
                                <textarea name="descricao" class="form-control" rows="5" placeholder="Descreva o que precisa ser feito"></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn-primary-mm">
                                    Enviar solicitação
                                </button>
                            </div>

                        </div>

                    </form>

                </div>
            </div>

        </div>

    </div>
</section>