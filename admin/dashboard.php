<?php
require_once __DIR__ . "/../config/database.php";

try {
    $sql = "
        SELECT 
            agendamentos.id_agendamento,
            agendamentos.servico_desejado,
            agendamentos.descricao,
            agendamentos.status,
            agendamentos.criado_em,
            clientes.nome,
            clientes.telefone,
            veiculos.modelo
        FROM agendamentos
        INNER JOIN clientes 
            ON agendamentos.id_cliente = clientes.id_cliente
        INNER JOIN veiculos 
            ON agendamentos.id_veiculo = veiculos.id_veiculo
        ORDER BY agendamentos.criado_em DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $erro) {
    die("Erro ao buscar agendamentos: " . $erro->getMessage());
}
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
            --red: #c62828;
        }

        body {
            background: var(--gray-bg);
            font-family: Arial, Helvetica, sans-serif;
            color: var(--black);
        }

        .admin-header {
            background: var(--black);
            color: var(--white);
            padding: 24px 0;
            border-bottom: 5px solid var(--green);
        }

        .admin-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .admin-header h1 {
            font-size: 28px;
            font-weight: 900;
            margin: 0;
        }

        .admin-header p {
            margin: 5px 0 0;
            color: #cccccc;
            font-weight: 700;
        }

        .admin-header a {
            background: var(--green);
            color: var(--white);
            text-decoration: none;
            padding: 11px 16px;
            font-weight: 900;
            white-space: nowrap;
        }

        .admin-header a:hover {
            background: var(--white);
            color: var(--black);
        }

        .admin-main {
            padding: 40px 0;
        }

        .admin-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-top: 5px solid var(--green);
            padding: 24px;
        }

        .admin-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .admin-title h2 {
            font-weight: 900;
            margin: 0;
            font-size: 28px;
        }

        .admin-title p {
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

        .table-area {
            display: block;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--black);
            color: var(--white);
            border: none;
            font-size: 14px;
            white-space: nowrap;
        }

        .table tbody td {
            vertical-align: middle;
            font-size: 14px;
        }

        .status {
            display: inline-block;
            background: var(--green-light);
            color: var(--green-dark);
            border-left: 4px solid var(--green);
            padding: 6px 10px;
            font-weight: 900;
            font-size: 13px;
            white-space: nowrap;
        }

        .empty-box {
            background: var(--white);
            border-left: 5px solid var(--green);
            padding: 18px;
            font-weight: 800;
        }

        .mobile-cards {
            display: none;
        }

        .mobile-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-top: 5px solid var(--green);
            padding: 18px;
            margin-bottom: 16px;
        }

        .mobile-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .mobile-card-header h3 {
            font-size: 20px;
            font-weight: 900;
            margin: 0;
        }

        .mobile-card-id {
            background: var(--black);
            color: var(--white);
            padding: 6px 9px;
            font-size: 13px;
            font-weight: 900;
            white-space: nowrap;
        }

        .mobile-info {
            display: grid;
            gap: 10px;
        }

        .mobile-info div {
            background: #f6f6f6;
            border-left: 4px solid var(--green);
            padding: 10px;
        }

        .mobile-info small {
            display: block;
            color: var(--gray-text);
            font-weight: 900;
            margin-bottom: 3px;
            text-transform: uppercase;
            font-size: 11px;
        }

        .mobile-info strong,
        .mobile-info span {
            color: var(--black);
            font-weight: 800;
        }

        .btn-voltar {
            display: inline-block;
            background: var(--green);
            color: var(--white);
            text-decoration: none;
            padding: 12px 18px;
            font-weight: 900;
            margin-top: 22px;
        }

        .btn-voltar:hover {
            background: var(--black);
            color: var(--white);
        }

        @media (max-width: 992px) {
            .admin-card {
                padding: 20px;
            }

            .admin-title {
                flex-direction: column;
                align-items: flex-start;
            }

            .badge-total {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 768px) {
            .admin-header {
                padding: 20px 0;
            }

            .admin-header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .admin-header h1 {
                font-size: 24px;
            }

            .admin-header a {
                width: 100%;
                text-align: center;
            }

            .admin-main {
                padding: 24px 0;
            }

            .admin-card {
                padding: 16px;
            }

            .admin-title h2 {
                font-size: 23px;
            }

            .table-area {
                display: none;
            }

            .mobile-cards {
                display: block;
            }

            .btn-voltar {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 420px) {
            .mobile-card {
                padding: 14px;
            }

            .mobile-card-header {
                flex-direction: column;
            }

            .mobile-card-id {
                width: fit-content;
            }
        }
    </style>
</head>
<body>

<header class="admin-header">
    <div class="container">
        <div class="admin-header-content">
            <div>
                <h1>Painel Administrativo</h1>
                <p>M&M Centro Estético Automotivo</p>
            </div>

            <a href="../index.php?page=home">
                Voltar para o site
            </a>
        </div>
    </div>
</header>

<main class="admin-main">
    <div class="container">

        <div class="admin-card">

            <div class="admin-title">
                <div>
                    <h2>Agendamentos recebidos</h2>
                    <p>Lista de solicitações enviadas pelo formulário do site.</p>
                </div>

                <div class="badge-total">
                    Total: <?php echo count($agendamentos); ?>
                </div>
            </div>

            <?php if (count($agendamentos) > 0): ?>

                <div class="table-area">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Telefone</th>
                                    <th>Veículo</th>
                                    <th>Serviço</th>
                                    <th>Descrição</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($agendamentos as $agendamento): ?>
                                    <tr>
                                        <td><?php echo $agendamento["id_agendamento"]; ?></td>

                                        <td>
                                            <strong>
                                                <?php echo htmlspecialchars($agendamento["nome"]); ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($agendamento["telefone"]); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($agendamento["modelo"]); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($agendamento["servico_desejado"]); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($agendamento["descricao"]); ?>
                                        </td>

                                        <td>
                                            <span class="status">
                                                <?php echo htmlspecialchars($agendamento["status"]); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php echo date("d/m/Y H:i", strtotime($agendamento["criado_em"])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mobile-cards">
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <div class="mobile-card">

                            <div class="mobile-card-header">
                                <h3>
                                    <?php echo htmlspecialchars($agendamento["nome"]); ?>
                                </h3>

                                <span class="mobile-card-id">
                                    #<?php echo $agendamento["id_agendamento"]; ?>
                                </span>
                            </div>

                            <div class="mobile-info">

                                <div>
                                    <small>Telefone</small>
                                    <span><?php echo htmlspecialchars($agendamento["telefone"]); ?></span>
                                </div>

                                <div>
                                    <small>Veículo</small>
                                    <span><?php echo htmlspecialchars($agendamento["modelo"]); ?></span>
                                </div>

                                <div>
                                    <small>Serviço</small>
                                    <span><?php echo htmlspecialchars($agendamento["servico_desejado"]); ?></span>
                                </div>

                                <div>
                                    <small>Descrição</small>
                                    <span><?php echo htmlspecialchars($agendamento["descricao"]); ?></span>
                                </div>

                                <div>
                                    <small>Status</small>
                                    <span class="status">
                                        <?php echo htmlspecialchars($agendamento["status"]); ?>
                                    </span>
                                </div>

                                <div>
                                    <small>Data</small>
                                    <strong>
                                        <?php echo date("d/m/Y H:i", strtotime($agendamento["criado_em"])); ?>
                                    </strong>
                                </div>

                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>

                <div class="empty-box">
                    Nenhum agendamento cadastrado até o momento.
                </div>

            <?php endif; ?>

        </div>

    </div>
</main>

</body>
</html>