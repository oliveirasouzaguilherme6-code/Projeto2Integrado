<?php
$servicos = [
    [
        "nome" => "Limpeza automotiva",
        "categoria" => "Estética",
        "descricao" => "Limpeza interna e externa para melhorar a apresentação e conservação do veículo."
    ],
    [
        "nome" => "Polimento automotivo",
        "categoria" => "Estética",
        "descricao" => "Serviço indicado para recuperar brilho e melhorar o acabamento da pintura."
    ],
    [
        "nome" => "Proteção de pintura",
        "categoria" => "Estética",
        "descricao" => "Aplicação de proteção para preservar a pintura e manter o veículo conservado."
    ],
    [
        "nome" => "Funilaria leve",
        "categoria" => "Funilaria",
        "descricao" => "Correção de pequenos danos, amassados e preparação da lataria."
    ],
    [
        "nome" => "Pintura de peças",
        "categoria" => "Pintura",
        "descricao" => "Pintura de para-choques, portas, paralamas e outras peças."
    ],
    [
        "nome" => "Troca de peças externas",
        "categoria" => "Peças",
        "descricao" => "Troca de para-choque, retrovisor, farol, lanterna e acabamentos."
    ]
];

$categoriaSelecionada = $_GET['categoria'] ?? 'Todos';

function filtrarServicos($servicos, $categoria) {
    $resultado = [];

    foreach ($servicos as $servico) {
        if ($categoria == 'Todos' || $servico['categoria'] == $categoria) {
            $resultado[] = $servico;
        }
    }

    return $resultado;
}

$servicosFiltrados = filtrarServicos($servicos, $categoriaSelecionada);
$categorias = ['Todos', 'Estética', 'Funilaria', 'Pintura', 'Peças'];
?>

<section class="section page-top">
    <div class="container">

        <div class="section-title center">
            <span>Serviços</span>
            <h1>Serviços automotivos da M&M</h1>
            <p>
                Estética automotiva, funilaria, pintura e troca de peças em um atendimento organizado.
            </p>
        </div>

        <div class="filter-area">
            <?php foreach ($categorias as $categoria): ?>
                <a 
                    href="index.php?page=servicos&categoria=<?php echo $categoria; ?>"
                    class="<?php echo $categoriaSelecionada == $categoria ? 'active-filter' : ''; ?>"
                >
                    <?php echo $categoria; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="row g-4">
            <?php foreach ($servicosFiltrados as $servico): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="service-list-card">
                        <span><?php echo $servico['categoria']; ?></span>
                        <h3><?php echo $servico['nome']; ?></h3>
                        <p><?php echo $servico['descricao']; ?></p>

                        <a href="index.php?page=agendamento" class="btn-card">
                            Solicitar avaliação
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<section class="section section-white">
    <div class="container">

        <div class="section-title center">
            <span>Processo</span>
            <h2>Como funciona o atendimento?</h2>
            <p>Uma sequência simples para organizar o serviço e entregar um bom resultado.</p>
        </div>

        <div class="row g-4">

            <div class="col-md-3">
                <div class="step-card">
                    <strong>1</strong>
                    <h3>Avaliação</h3>
                    <p>Verificamos o estado do veículo.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="step-card">
                    <strong>2</strong>
                    <h3>Orçamento</h3>
                    <p>Indicamos o serviço adequado.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="step-card">
                    <strong>3</strong>
                    <h3>Execução</h3>
                    <p>Realizamos o serviço combinado.</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="step-card">
                    <strong>4</strong>
                    <h3>Entrega</h3>
                    <p>Revisamos o acabamento final.</p>
                </div>
            </div>

        </div>

    </div>
</section>