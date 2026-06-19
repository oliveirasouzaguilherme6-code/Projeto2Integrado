<?php
require_once __DIR__ . "/../config/database.php";

$mensagem = "";
$tipoMensagem = "";

/* =========================
   AÇÕES: ATIVAR / DESATIVAR
========================= */

if (isset($_GET["tipo"], $_GET["acao"], $_GET["id"])) {
    $tipo = $_GET["tipo"];
    $acao = $_GET["acao"];
    $id = intval($_GET["id"]);

    if ($acao == "ativar" || $acao == "desativar") {
        $novoStatus = $acao == "ativar" ? 1 : 0;

        try {
            if ($tipo == "servico") {
                $sql = "UPDATE servicos SET ativo = :ativo WHERE id_servico = :id";
                $destino = "#servicos";
            } elseif ($tipo == "peca") {
                $sql = "UPDATE pecas SET ativo = :ativo WHERE id_peca = :id";
                $destino = "#pecas";
            } else {
                $sql = "";
                $destino = "";
            }

            if ($sql != "") {
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":ativo", $novoStatus, PDO::PARAM_INT);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
            }

            header("Location: dashboard.php" . $destino);
            exit;

        } catch (PDOException $erro) {
            $mensagem = "Erro ao atualizar status: " . $erro->getMessage();
            $tipoMensagem = "erro";
        }
    }
}

/* =========================
   CADASTRAR SERVIÇO
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["form_tipo"] ?? "") == "servico") {
    $nome = trim($_POST["nome"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $precoBase = str_replace(",", ".", trim($_POST["preco_base"] ?? "0"));

    if ($nome == "" || $categoria == "") {
        $mensagem = "Preencha o nome e a categoria do serviço.";
        $tipoMensagem = "erro";
    } elseif (!is_numeric($precoBase) || $precoBase < 0) {
        $mensagem = "Informe um preço base válido.";
        $tipoMensagem = "erro";
    } else {
        try {
            $sql = "
                INSERT INTO servicos 
                (nome, categoria, descricao, preco_base, ativo)
                VALUES
                (:nome, :categoria, :descricao, :preco_base, 1)
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":categoria", $categoria);
            $stmt->bindParam(":descricao", $descricao);
            $stmt->bindParam(":preco_base", $precoBase);
            $stmt->execute();

            $mensagem = "Serviço cadastrado com sucesso!";
            $tipoMensagem = "sucesso";

        } catch (PDOException $erro) {
            $mensagem = "Erro ao cadastrar serviço: " . $erro->getMessage();
            $tipoMensagem = "erro";
        }
    }
}

/* =========================
   CADASTRAR PEÇA COM UPLOAD
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["form_tipo"] ?? "") == "peca") {
    $nome = trim($_POST["nome"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $preco = str_replace(",", ".", trim($_POST["preco"] ?? "0"));
    $condicao = trim($_POST["condicao"] ?? "");
    $origem = trim($_POST["origem"] ?? "");
    $compatibilidade = trim($_POST["compatibilidade"] ?? "");
    $observacao = trim($_POST["observacao"] ?? "");
    $imagem = "";

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $pastaDestino = __DIR__ . "/../assets/img/pecas/";

        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0777, true);
        }

        $nomeOriginal = $_FILES["imagem"]["name"];
        $tamanhoArquivo = $_FILES["imagem"]["size"];
        $arquivoTemporario = $_FILES["imagem"]["tmp_name"];
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

        $extensoesPermitidas = ["jpg", "jpeg", "png", "webp"];
        $tamanhoMaximo = 5 * 1024 * 1024;

        if (!in_array($extensao, $extensoesPermitidas)) {
            $mensagem = "Formato de imagem inválido. Use JPG, PNG ou WEBP.";
            $tipoMensagem = "erro";
        } elseif ($tamanhoArquivo > $tamanhoMaximo) {
            $mensagem = "A imagem é muito grande. Envie uma imagem de até 5MB.";
            $tipoMensagem = "erro";
        } else {
            $novoNome = uniqid("peca_") . "." . $extensao;
            $caminhoFinal = $pastaDestino . $novoNome;

            if (move_uploaded_file($arquivoTemporario, $caminhoFinal)) {
                $imagem = $novoNome;
            } else {
                $mensagem = "Erro ao salvar a imagem da peça.";
                $tipoMensagem = "erro";
            }
        }
    }

    if ($mensagem != "") {
        // Já existe erro anterior, como imagem inválida.
    } elseif ($nome == "" || $categoria == "" || $condicao == "" || $origem == "") {
        $mensagem = "Preencha nome, categoria, condição e origem da peça.";
        $tipoMensagem = "erro";
    } elseif (!is_numeric($preco) || $preco < 0) {
        $mensagem = "Informe um preço válido para a peça.";
        $tipoMensagem = "erro";
    } else {
        try {
            $sql = "
                INSERT INTO pecas
                (nome, categoria, descricao, preco, condicao, origem, imagem, compatibilidade, observacao, ativo)
                VALUES
                (:nome, :categoria, :descricao, :preco, :condicao, :origem, :imagem, :compatibilidade, :observacao, 1)
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":categoria", $categoria);
            $stmt->bindParam(":descricao", $descricao);
            $stmt->bindParam(":preco", $preco);
            $stmt->bindParam(":condicao", $condicao);
            $stmt->bindParam(":origem", $origem);
            $stmt->bindParam(":imagem", $imagem);
            $stmt->bindParam(":compatibilidade", $compatibilidade);
            $stmt->bindParam(":observacao", $observacao);
            $stmt->execute();

            $mensagem = "Peça cadastrada com sucesso!";
            $tipoMensagem = "sucesso";

        } catch (PDOException $erro) {
            $mensagem = "Erro ao cadastrar peça: " . $erro->getMessage();
            $tipoMensagem = "erro";
        }
    }
}

/* =========================
   BUSCAS DO PAINEL
========================= */

try {
    $sqlAgendamentos = "
        SELECT 
            agendamentos.id_agendamento,
            agendamentos.servico_desejado,
            agendamentos.descricao,
            agendamentos.status,
            agendamentos.criado_em,
            clientes.nome,
            clientes.telefone,
            veiculos.modelo,
            COALESCE(
                GROUP_CONCAT(servicos.nome SEPARATOR ', '),
                agendamentos.servico_desejado
            ) AS servicos_escolhidos
        FROM agendamentos
        INNER JOIN clientes 
            ON agendamentos.id_cliente = clientes.id_cliente
        INNER JOIN veiculos 
            ON agendamentos.id_veiculo = veiculos.id_veiculo
        LEFT JOIN agendamento_servicos
            ON agendamentos.id_agendamento = agendamento_servicos.id_agendamento
        LEFT JOIN servicos
            ON agendamento_servicos.id_servico = servicos.id_servico
        GROUP BY 
            agendamentos.id_agendamento,
            agendamentos.servico_desejado,
            agendamentos.descricao,
            agendamentos.status,
            agendamentos.criado_em,
            clientes.nome,
            clientes.telefone,
            veiculos.modelo
        ORDER BY agendamentos.criado_em DESC
    ";

    $stmtAgendamentos = $pdo->prepare($sqlAgendamentos);
    $stmtAgendamentos->execute();
    $agendamentos = $stmtAgendamentos->fetchAll(PDO::FETCH_ASSOC);

    $stmtServicos = $pdo->prepare("SELECT * FROM servicos ORDER BY ativo DESC, categoria ASC, nome ASC");
    $stmtServicos->execute();
    $servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);

    $stmtPecas = $pdo->prepare("SELECT * FROM pecas ORDER BY ativo DESC, categoria ASC, nome ASC");
    $stmtPecas->execute();
    $pecas = $stmtPecas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $erro) {
    die("Erro ao buscar dados do painel: " . $erro->getMessage());
}

/* =========================
   FUNÇÕES
========================= */

function formatarPrecoAdmin($valor) {
    if ($valor <= 0) {
        return "Sob consulta";
    }

    return "R$ " . number_format($valor, 2, ",", ".");
}

$servicosAtivos = array_filter($servicos, function ($servico) {
    return $servico["ativo"] == 1;
});

$pecasAtivas = array_filter($pecas, function ($peca) {
    return $peca["ativo"] == 1;
});
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel Administrativo - M&M</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --black: #111111;
            --green: #159447;
            --green-dark: #0f6f35;
            --green-light: #e9f7ee;
            --white: #ffffff;
            --gray-bg: #eeeeee;
            --gray-text: #555555;
            --border: #d1d1d1;
            --orange: #f36c12;
            --red: #c62828;
        }

        body {
            background: var(--gray-bg);
            font-family: Arial, Helvetica, sans-serif;
            color: var(--black);
        }

        .admin-layout {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 280px 1fr;
        }

        .sidebar {
            background: var(--black);
            color: var(--white);
            padding: 28px 22px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .sidebar-brand {
            margin-bottom: 30px;
        }

        .sidebar-brand h1 {
            font-size: 26px;
            font-weight: 900;
            margin: 0;
        }

        .sidebar-brand p {
            color: #cccccc;
            margin: 6px 0 0;
            font-weight: 700;
        }

        .sidebar-menu {
            display: grid;
            gap: 10px;
        }

        .tab-button,
        .site-button {
            border: none;
            background: #1d1d1d;
            color: var(--white);
            padding: 14px 16px;
            font-weight: 900;
            text-align: left;
            text-decoration: none;
            border-left: 5px solid transparent;
            cursor: pointer;
            width: 100%;
        }

        .tab-button:hover,
        .tab-button.active,
        .site-button:hover {
            background: var(--green);
            border-left-color: var(--white);
            color: var(--white);
        }

        .main-area {
            padding: 32px;
        }

        .topbar {
            background: var(--white);
            border: 1px solid var(--border);
            border-top: 5px solid var(--green);
            padding: 24px;
            margin-bottom: 26px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .topbar h2 {
            font-size: 30px;
            font-weight: 900;
            margin: 0;
        }

        .topbar p {
            color: var(--gray-text);
            margin: 6px 0 0;
        }

        .quick-help {
            background: var(--green-light);
            color: var(--green-dark);
            padding: 12px 16px;
            border-left: 5px solid var(--green);
            font-weight: 800;
            max-width: 390px;
        }

        .message-box {
            border-left: 5px solid var(--green);
            padding: 14px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .message-success {
            background: var(--green-light);
            border-left-color: var(--green);
            color: var(--green-dark);
        }

        .message-error {
            background: #fff1f1;
            border-left-color: var(--red);
            color: #8b0000;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 26px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-top: 5px solid var(--green);
            padding: 24px;
        }

        .stat-card span {
            display: block;
            color: var(--gray-text);
            font-weight: 900;
            text-transform: uppercase;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .stat-card strong {
            display: block;
            font-size: 40px;
            font-weight: 900;
            line-height: 1;
        }

        .stat-card small {
            display: block;
            color: var(--gray-text);
            margin-top: 8px;
            font-weight: 700;
        }

        .content-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-top: 5px solid var(--green);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-title-area {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 22px;
        }

        .card-title-area h3 {
            font-size: 26px;
            font-weight: 900;
            margin: 0;
        }

        .card-title-area p {
            color: var(--gray-text);
            margin: 6px 0 0;
        }

        .badge-total {
            background: var(--green);
            color: var(--white);
            padding: 10px 14px;
            font-weight: 900;
            white-space: nowrap;
        }

        label {
            font-weight: 900;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 0;
            border: 1px solid var(--border);
            padding: 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--green);
            box-shadow: none;
        }

        .btn-main {
            border: none;
            background: var(--green);
            color: var(--white);
            padding: 13px 20px;
            font-weight: 900;
            cursor: pointer;
        }

        .btn-main:hover {
            background: var(--black);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--black);
            color: var(--white);
            border: none;
            white-space: nowrap;
            font-size: 14px;
        }

        .table tbody td {
            vertical-align: middle;
            font-size: 14px;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            font-weight: 900;
            font-size: 13px;
            white-space: nowrap;
        }

        .status.ativo,
        .status.novo {
            background: var(--green-light);
            color: var(--green-dark);
            border-left: 4px solid var(--green);
        }

        .status.inativo {
            background: #f5f5f5;
            color: var(--gray-text);
            border-left: 4px solid #777777;
        }

        .btn-small {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            font-weight: 900;
            font-size: 13px;
            color: var(--white);
        }

        .btn-ativar {
            background: var(--green);
        }

        .btn-desativar {
            background: var(--orange);
        }

        .btn-small:hover {
            background: var(--black);
            color: var(--white);
        }

        .empty-box {
            background: #f7f7f7;
            border-left: 5px solid var(--green);
            padding: 16px;
            font-weight: 800;
        }

        .simple-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .simple-actions button {
            background: var(--green);
            color: var(--white);
            border: none;
            padding: 12px 16px;
            font-weight: 900;
            cursor: pointer;
        }

        .simple-actions button:hover {
            background: var(--black);
        }

        .image-preview {
            width: 86px;
            height: 64px;
            background: #dddddd;
            border: 1px solid var(--border);
            object-fit: cover;
            display: block;
        }

        .image-empty {
            width: 86px;
            height: 64px;
            background: #f3f3f3;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-text);
            font-size: 11px;
            font-weight: 900;
            text-align: center;
            padding: 4px;
        }

        .upload-hint {
            display: block;
            color: var(--gray-text);
            margin-top: 6px;
            font-weight: 700;
        }

        @media (max-width: 1100px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                height: auto;
                position: relative;
            }

            .sidebar-menu {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .main-area {
                padding: 18px;
            }

            .sidebar {
                padding: 22px 18px;
            }

            .sidebar-menu {
                grid-template-columns: 1fr;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar h2 {
                font-size: 24px;
            }

            .quick-help {
                max-width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .content-card {
                padding: 18px;
            }

            .card-title-area {
                flex-direction: column;
            }

            .badge-total {
                width: 100%;
                text-align: center;
            }

            .btn-main {
                width: 100%;
            }

            .table thead {
                display: none;
            }

            .table,
            .table tbody,
            .table tr,
            .table td {
                display: block;
                width: 100%;
            }

            .table tr {
                background: var(--white);
                border: 1px solid var(--border);
                border-top: 5px solid var(--green);
                margin-bottom: 16px;
                padding: 10px;
            }

            .table td {
                border: none;
                border-bottom: 1px solid #eeeeee;
                padding: 10px;
            }

            .table td:last-child {
                border-bottom: none;
            }

            .table td::before {
                content: attr(data-label);
                display: block;
                color: var(--gray-text);
                font-weight: 900;
                font-size: 11px;
                text-transform: uppercase;
                margin-bottom: 4px;
            }

            .image-preview,
            .image-empty {
                width: 100%;
                height: 180px;
            }
        }
    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="sidebar-brand">
            <h1>Painel M&M</h1>
            <p>Controle simples da empresa</p>
        </div>

        <nav class="sidebar-menu">
            <button class="tab-button active" data-tab="visao">Visão geral</button>
            <button class="tab-button" data-tab="agendamentos">Agendamentos</button>
            <button class="tab-button" data-tab="servicos">Serviços</button>
            <button class="tab-button" data-tab="pecas">Peças</button>
            <a href="../index.php?page=home" class="site-button">Voltar ao site</a>
        </nav>
    </aside>

    <main class="main-area">

        <section class="topbar">
            <div>
                <h2>Área administrativa</h2>
                <p>Gerencie pedidos, serviços e peças sem acessar o phpMyAdmin.</p>
            </div>

            <div class="quick-help">
                Use o menu lateral. Cada botão abre apenas uma área por vez.
            </div>
        </section>

        <?php if ($mensagem != ""): ?>
            <div class="message-box <?php echo $tipoMensagem == 'erro' ? 'message-error' : 'message-success'; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <section class="tab-panel active" id="tab-visao">

            <div class="stats-grid">
                <div class="stat-card">
                    <span>Agendamentos</span>
                    <strong><?php echo count($agendamentos); ?></strong>
                    <small>Solicitações recebidas pelo site</small>
                </div>

                <div class="stat-card">
                    <span>Serviços ativos</span>
                    <strong><?php echo count($servicosAtivos); ?></strong>
                    <small>Serviços disponíveis para o cliente</small>
                </div>

                <div class="stat-card">
                    <span>Peças ativas</span>
                    <strong><?php echo count($pecasAtivas); ?></strong>
                    <small>Peças visíveis no catálogo</small>
                </div>
            </div>

            <div class="content-card">
                <div class="card-title-area">
                    <div>
                        <h3>Como usar o painel?</h3>
                        <p>Painel simples para a empresa controlar o sistema sem mexer em código.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="empty-box">
                            <strong>1. Ver agendamentos</strong>
                            <br>
                            Veja os clientes que pediram avaliação.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="empty-box">
                            <strong>2. Cadastrar serviços</strong>
                            <br>
                            Adicione opções que aparecem no formulário.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="empty-box">
                            <strong>3. Cadastrar peças</strong>
                            <br>
                            Envie foto, preço e informações para o catálogo.
                        </div>
                    </div>
                </div>

                <div class="simple-actions">
                    <button type="button" data-open="agendamentos">Ver agendamentos</button>
                    <button type="button" data-open="servicos">Gerenciar serviços</button>
                    <button type="button" data-open="pecas">Gerenciar peças</button>
                </div>
            </div>

        </section>

        <section class="tab-panel" id="tab-agendamentos">

            <div class="content-card">
                <div class="card-title-area">
                    <div>
                        <h3>Agendamentos recebidos</h3>
                        <p>Solicitações enviadas pelos clientes no site.</p>
                    </div>

                    <span class="badge-total">
                        Total: <?php echo count($agendamentos); ?>
                    </span>
                </div>

                <?php if (count($agendamentos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Telefone</th>
                                    <th>Veículo</th>
                                    <th>Serviços</th>
                                    <th>Descrição</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($agendamentos as $agendamento): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo $agendamento["id_agendamento"]; ?></td>

                                        <td data-label="Cliente">
                                            <strong><?php echo htmlspecialchars($agendamento["nome"]); ?></strong>
                                        </td>

                                        <td data-label="Telefone">
                                            <?php echo htmlspecialchars($agendamento["telefone"]); ?>
                                        </td>

                                        <td data-label="Veículo">
                                            <?php echo htmlspecialchars($agendamento["modelo"]); ?>
                                        </td>

                                        <td data-label="Serviços">
                                            <?php echo htmlspecialchars($agendamento["servicos_escolhidos"]); ?>
                                        </td>

                                        <td data-label="Descrição">
                                            <?php echo htmlspecialchars($agendamento["descricao"]); ?>
                                        </td>

                                        <td data-label="Status">
                                            <span class="status novo">
                                                <?php echo htmlspecialchars($agendamento["status"]); ?>
                                            </span>
                                        </td>

                                        <td data-label="Data">
                                            <?php echo date("d/m/Y H:i", strtotime($agendamento["criado_em"])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-box">
                        Nenhum agendamento cadastrado.
                    </div>
                <?php endif; ?>
            </div>

        </section>

        <section class="tab-panel" id="tab-servicos">

            <div class="content-card">
                <div class="card-title-area">
                    <div>
                        <h3>Cadastrar serviço</h3>
                        <p>Esses serviços aparecem no formulário de agendamento.</p>
                    </div>
                </div>

                <form method="POST" action="dashboard.php#servicos">
                    <input type="hidden" name="form_tipo" value="servico">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Nome do serviço *</label>
                            <input type="text" name="nome" class="form-control" placeholder="Ex: Higienização interna" required>
                        </div>

                        <div class="col-md-6">
                            <label>Categoria *</label>
                            <select name="categoria" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="Estética">Estética</option>
                                <option value="Funilaria">Funilaria</option>
                                <option value="Pintura">Pintura</option>
                                <option value="Peças">Peças</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Preço base</label>
                            <input type="text" name="preco_base" class="form-control" placeholder="Ex: 180.00">
                        </div>

                        <div class="col-12">
                            <label>Descrição</label>
                            <textarea name="descricao" class="form-control" rows="4" placeholder="Descreva o serviço"></textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn-main">
                                Cadastrar serviço
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="content-card">
                <div class="card-title-area">
                    <div>
                        <h3>Serviços cadastrados</h3>
                        <p>Ative ou desative serviços do formulário.</p>
                    </div>

                    <span class="badge-total">
                        Total: <?php echo count($servicos); ?>
                    </span>
                </div>

                <?php if (count($servicos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Serviço</th>
                                    <th>Categoria</th>
                                    <th>Preço base</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($servicos as $servico): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo $servico["id_servico"]; ?></td>

                                        <td data-label="Serviço">
                                            <strong><?php echo htmlspecialchars($servico["nome"]); ?></strong>
                                            <br>
                                            <small><?php echo htmlspecialchars($servico["descricao"]); ?></small>
                                        </td>

                                        <td data-label="Categoria">
                                            <?php echo htmlspecialchars($servico["categoria"]); ?>
                                        </td>

                                        <td data-label="Preço base">
                                            <?php echo formatarPrecoAdmin($servico["preco_base"]); ?>
                                        </td>

                                        <td data-label="Status">
                                            <?php if ($servico["ativo"] == 1): ?>
                                                <span class="status ativo">Ativo</span>
                                            <?php else: ?>
                                                <span class="status inativo">Inativo</span>
                                            <?php endif; ?>
                                        </td>

                                        <td data-label="Ação">
                                            <?php if ($servico["ativo"] == 1): ?>
                                                <a href="dashboard.php?tipo=servico&acao=desativar&id=<?php echo $servico['id_servico']; ?>#servicos" class="btn-small btn-desativar">
                                                    Desativar
                                                </a>
                                            <?php else: ?>
                                                <a href="dashboard.php?tipo=servico&acao=ativar&id=<?php echo $servico['id_servico']; ?>#servicos" class="btn-small btn-ativar">
                                                    Ativar
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-box">
                        Nenhum serviço cadastrado.
                    </div>
                <?php endif; ?>
            </div>

        </section>

        <section class="tab-panel" id="tab-pecas">

            <div class="content-card">
                <div class="card-title-area">
                    <div>
                        <h3>Cadastrar peça</h3>
                        <p>Selecione uma imagem e cadastre a peça no catálogo.</p>
                    </div>
                </div>

                <form method="POST" action="dashboard.php#pecas" enctype="multipart/form-data">
                    <input type="hidden" name="form_tipo" value="peca">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Nome da peça *</label>
                            <input type="text" name="nome" class="form-control" placeholder="Ex: Capô" required>
                        </div>

                        <div class="col-md-6">
                            <label>Categoria *</label>
                            <input type="text" name="categoria" class="form-control" placeholder="Ex: Lataria" required>
                        </div>

                        <div class="col-md-4">
                            <label>Preço</label>
                            <input type="text" name="preco" class="form-control" placeholder="Ex: 650.00">
                        </div>

                        <div class="col-md-4">
                            <label>Condição *</label>
                            <select name="condicao" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="Novo">Novo</option>
                                <option value="Usado">Usado</option>
                                <option value="Recondicionada">Recondicionada</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Origem *</label>
                            <select name="origem" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="Original">Original</option>
                                <option value="Paralela">Paralela</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label>Imagem da peça</label>
                            <input 
                                type="file" 
                                name="imagem" 
                                class="form-control" 
                                accept="image/jpeg, image/png, image/webp"
                            >
                            <small class="upload-hint">
                                Selecione uma imagem JPG, PNG ou WEBP. O sistema salva automaticamente.
                            </small>
                        </div>

                        <div class="col-12">
                            <label>Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3" placeholder="Descreva a peça"></textarea>
                        </div>

                        <div class="col-12">
                            <label>Compatibilidade</label>
                            <textarea name="compatibilidade" class="form-control" rows="3" placeholder="Ex: Compatível conforme modelo e ano do veículo"></textarea>
                        </div>

                        <div class="col-12">
                            <label>Observação</label>
                            <textarea name="observacao" class="form-control" rows="3" placeholder="Informações extras sobre a peça"></textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn-main">
                                Cadastrar peça
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="content-card">
                <div class="card-title-area">
                    <div>
                        <h3>Peças cadastradas</h3>
                        <p>Ative ou desative peças do catálogo.</p>
                    </div>

                    <span class="badge-total">
                        Total: <?php echo count($pecas); ?>
                    </span>
                </div>

                <?php if (count($pecas) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagem</th>
                                    <th>Peça</th>
                                    <th>Categoria</th>
                                    <th>Preço</th>
                                    <th>Condição</th>
                                    <th>Origem</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($pecas as $peca): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo $peca["id_peca"]; ?></td>

                                        <td data-label="Imagem">
                                            <?php if (!empty($peca["imagem"])): ?>
                                                <img 
                                                    src="../assets/img/pecas/<?php echo htmlspecialchars($peca["imagem"]); ?>" 
                                                    alt="<?php echo htmlspecialchars($peca["nome"]); ?>"
                                                    class="image-preview"
                                                >
                                            <?php else: ?>
                                                <div class="image-empty">
                                                    Sem imagem
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td data-label="Peça">
                                            <strong><?php echo htmlspecialchars($peca["nome"]); ?></strong>
                                            <br>
                                            <small><?php echo htmlspecialchars($peca["descricao"]); ?></small>
                                        </td>

                                        <td data-label="Categoria">
                                            <?php echo htmlspecialchars($peca["categoria"]); ?>
                                        </td>

                                        <td data-label="Preço">
                                            <?php echo formatarPrecoAdmin($peca["preco"]); ?>
                                        </td>

                                        <td data-label="Condição">
                                            <?php echo htmlspecialchars($peca["condicao"]); ?>
                                        </td>

                                        <td data-label="Origem">
                                            <?php echo htmlspecialchars($peca["origem"]); ?>
                                        </td>

                                        <td data-label="Status">
                                            <?php if ($peca["ativo"] == 1): ?>
                                                <span class="status ativo">Ativo</span>
                                            <?php else: ?>
                                                <span class="status inativo">Inativo</span>
                                            <?php endif; ?>
                                        </td>

                                        <td data-label="Ação">
                                            <?php if ($peca["ativo"] == 1): ?>
                                                <a href="dashboard.php?tipo=peca&acao=desativar&id=<?php echo $peca['id_peca']; ?>#pecas" class="btn-small btn-desativar">
                                                    Desativar
                                                </a>
                                            <?php else: ?>
                                                <a href="dashboard.php?tipo=peca&acao=ativar&id=<?php echo $peca['id_peca']; ?>#pecas" class="btn-small btn-ativar">
                                                    Ativar
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-box">
                        Nenhuma peça cadastrada.
                    </div>
                <?php endif; ?>
            </div>

        </section>

    </main>

</div>

<script>
    const tabButtons = document.querySelectorAll(".tab-button");
    const tabPanels = document.querySelectorAll(".tab-panel");
    const openButtons = document.querySelectorAll("[data-open]");

    function openTab(tabName) {
        tabButtons.forEach(function (button) {
            button.classList.remove("active");

            if (button.dataset.tab === tabName) {
                button.classList.add("active");
            }
        });

        tabPanels.forEach(function (panel) {
            panel.classList.remove("active");
        });

        const activePanel = document.getElementById("tab-" + tabName);

        if (activePanel) {
            activePanel.classList.add("active");
            window.location.hash = tabName;
        }
    }

    tabButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            openTab(button.dataset.tab);
        });
    });

    openButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            openTab(button.dataset.open);
        });
    });

    const hash = window.location.hash.replace("#", "");

    if (hash) {
        openTab(hash);
    }
</script>

</body>
</html>