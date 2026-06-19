<?php
require_once __DIR__ . "/../config/database.php";

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $veiculo = trim($_POST["veiculo"] ?? "");
    $servico = trim($_POST["servico"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");

    if ($nome == "" || $telefone == "" || $veiculo == "" || $servico == "") {
        $mensagem = "Preencha todos os campos obrigatórios.";
        $tipoMensagem = "erro";
    } else {
        try {
            $pdo->beginTransaction();

            $sqlCliente = "INSERT INTO clientes (nome, telefone) VALUES (:nome, :telefone)";
            $stmtCliente = $pdo->prepare($sqlCliente);
            $stmtCliente->bindParam(":nome", $nome);
            $stmtCliente->bindParam(":telefone", $telefone);
            $stmtCliente->execute();

            $idCliente = $pdo->lastInsertId();

            $sqlVeiculo = "INSERT INTO veiculos (id_cliente, modelo) VALUES (:id_cliente, :modelo)";
            $stmtVeiculo = $pdo->prepare($sqlVeiculo);
            $stmtVeiculo->bindParam(":id_cliente", $idCliente);
            $stmtVeiculo->bindParam(":modelo", $veiculo);
            $stmtVeiculo->execute();

            $idVeiculo = $pdo->lastInsertId();

            $sqlAgendamento = "
                INSERT INTO agendamentos 
                (id_cliente, id_veiculo, servico_desejado, descricao, status) 
                VALUES 
                (:id_cliente, :id_veiculo, :servico_desejado, :descricao, 'Novo')
            ";

            $stmtAgendamento = $pdo->prepare($sqlAgendamento);
            $stmtAgendamento->bindParam(":id_cliente", $idCliente);
            $stmtAgendamento->bindParam(":id_veiculo", $idVeiculo);
            $stmtAgendamento->bindParam(":servico_desejado", $servico);
            $stmtAgendamento->bindParam(":descricao", $descricao);
            $stmtAgendamento->execute();

            $pdo->commit();

            $mensagem = "Solicitação enviada com sucesso! Em breve entraremos em contato.";
            $tipoMensagem = "sucesso";

        } catch (PDOException $erro) {
            $pdo->rollBack();
            $mensagem = "Erro ao salvar solicitação: " . $erro->getMessage();
            $tipoMensagem = "erro";
        }
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

                    <div class="plain-list">
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
                        <div class="message-box <?php echo $tipoMensagem == 'erro' ? 'message-error' : 'message-success'; ?>">
                            <?php echo htmlspecialchars($mensagem); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label>Nome completo *</label>
                                <input 
                                    type="text" 
                                    name="nome" 
                                    class="form-control" 
                                    placeholder="Digite seu nome"
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label>Telefone / WhatsApp *</label>
                                <input 
                                    type="text" 
                                    name="telefone" 
                                    class="form-control" 
                                    placeholder="(44) 99999-9999"
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label>Veículo *</label>
                                <input 
                                    type="text" 
                                    name="veiculo" 
                                    class="form-control" 
                                    placeholder="Ex: Corolla 2018"
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label>Serviço desejado *</label>
                                <select name="servico" class="form-select" required>
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
                                <textarea 
                                    name="descricao" 
                                    class="form-control" 
                                    rows="5" 
                                    placeholder="Descreva o que precisa ser feito"
                                ></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn-main">
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