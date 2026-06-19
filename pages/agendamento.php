<?php
require_once __DIR__ . "/../config/database.php";

$mensagem = "";
$tipoMensagem = "";

try {
    $sqlServicos = "SELECT id_servico, nome, categoria FROM servicos WHERE ativo = 1 ORDER BY categoria, nome";
    $stmtServicos = $pdo->prepare($sqlServicos);
    $stmtServicos->execute();
    $servicosDisponiveis = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $erro) {
    die("Erro ao buscar serviços: " . $erro->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $veiculo = trim($_POST["veiculo"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $servicosSelecionados = $_POST["servicos"] ?? [];

    if ($nome == "" || $telefone == "" || $veiculo == "" || empty($servicosSelecionados)) {
        $mensagem = "Preencha todos os campos obrigatórios e selecione pelo menos um serviço.";
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

            $idsValidos = array_map("intval", $servicosSelecionados);
            $placeholders = implode(",", array_fill(0, count($idsValidos), "?"));

            $sqlBuscaNomes = "SELECT nome FROM servicos WHERE id_servico IN ($placeholders)";
            $stmtBuscaNomes = $pdo->prepare($sqlBuscaNomes);
            $stmtBuscaNomes->execute($idsValidos);
            $nomesServicos = $stmtBuscaNomes->fetchAll(PDO::FETCH_COLUMN);

            $servicoTexto = implode(", ", $nomesServicos);

            $sqlAgendamento = "
                INSERT INTO agendamentos 
                (id_cliente, id_veiculo, servico_desejado, descricao, status) 
                VALUES 
                (:id_cliente, :id_veiculo, :servico_desejado, :descricao, 'Novo')
            ";

            $stmtAgendamento = $pdo->prepare($sqlAgendamento);
            $stmtAgendamento->bindParam(":id_cliente", $idCliente);
            $stmtAgendamento->bindParam(":id_veiculo", $idVeiculo);
            $stmtAgendamento->bindParam(":servico_desejado", $servicoTexto);
            $stmtAgendamento->bindParam(":descricao", $descricao);
            $stmtAgendamento->execute();

            $idAgendamento = $pdo->lastInsertId();

            $sqlAgendamentoServico = "
                INSERT INTO agendamento_servicos 
                (id_agendamento, id_servico) 
                VALUES 
                (:id_agendamento, :id_servico)
            ";

            $stmtAgendamentoServico = $pdo->prepare($sqlAgendamentoServico);

            foreach ($idsValidos as $idServico) {
                $stmtAgendamentoServico->bindParam(":id_agendamento", $idAgendamento);
                $stmtAgendamentoServico->bindParam(":id_servico", $idServico);
                $stmtAgendamentoServico->execute();
            }

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
                Informe os dados do veículo, selecione os serviços desejados e descreva o que precisa ser feito.
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
                        <div>Selecione um ou mais serviços</div>
                        <div>Descreva os detalhes do veículo</div>
                        <div>A equipe avalia a solicitação</div>
                        <div>Orçamento sob análise</div>
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

                            <div class="col-12">
                                <label>Veículo *</label>
                                <input 
                                    type="text" 
                                    name="veiculo" 
                                    class="form-control" 
                                    placeholder="Ex: Corolla 2018"
                                    required
                                >
                            </div>

                            <div class="col-12">
                                <label>Serviços desejados *</label>

                                <div class="services-checkbox-area">
                                    <?php foreach ($servicosDisponiveis as $servico): ?>
                                        <label class="service-checkbox">
                                            <input 
                                                type="checkbox" 
                                                name="servicos[]" 
                                                value="<?php echo $servico['id_servico']; ?>"
                                            >

                                            <span>
                                                <strong><?php echo htmlspecialchars($servico["nome"]); ?></strong>
                                                <small><?php echo htmlspecialchars($servico["categoria"]); ?></small>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="col-12">
                                <label>Descrição detalhada</label>
                                <textarea 
                                    name="descricao" 
                                    class="form-control" 
                                    rows="6" 
                                    placeholder="Explique o que aconteceu, qual parte do veículo precisa de atenção, se há amassado, risco, peça quebrada, pintura danificada ou outro detalhe importante."
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