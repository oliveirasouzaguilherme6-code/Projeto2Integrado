<?php
$pecas = [
    [
        "nome" => "Para-choque dianteiro",
        "categoria" => "Peças externas",
        "descricao" => "Peça utilizada para substituição em veículos com danos na parte dianteira.",
        "preco" => 450,
        "condicao" => "Novo",
        "origem" => "Paralela",
        "imagem" => "parachoque-dianteiro.jpg",
        "compatibilidade" => "Compatível conforme modelo e ano do veículo.",
        "observacao" => "Valor pode variar conforme o modelo do carro e disponibilidade da peça."
    ],
    [
        "nome" => "Para-choque traseiro",
        "categoria" => "Peças externas",
        "descricao" => "Indicado para troca em casos de trincas, quebras ou danos traseiros.",
        "preco" => 480,
        "condicao" => "Novo",
        "origem" => "Paralela",
        "imagem" => "parachoque-traseiro.jpg",
        "compatibilidade" => "Necessário confirmar o modelo antes da compra.",
        "observacao" => "Pode precisar de pintura antes da instalação."
    ],
    [
        "nome" => "Retrovisor",
        "categoria" => "Acessórios externos",
        "descricao" => "Substituição de retrovisor danificado conforme modelo do veículo.",
        "preco" => 180,
        "condicao" => "Usado",
        "origem" => "Original",
        "imagem" => "retrovisor.jpg",
        "compatibilidade" => "Compatível conforme lado, modelo e versão do veículo.",
        "observacao" => "Peça usada em bom estado, sujeita à avaliação visual."
    ],
    [
        "nome" => "Farol dianteiro",
        "categoria" => "Iluminação",
        "descricao" => "Troca de farol dianteiro para melhorar segurança e aparência.",
        "preco" => 320,
        "condicao" => "Novo",
        "origem" => "Paralela",
        "imagem" => "farol.jpg",
        "compatibilidade" => "Compatibilidade depende do modelo e ano do veículo.",
        "observacao" => "Pode haver diferença entre versões com máscara negra ou cromada."
    ],
    [
        "nome" => "Lanterna traseira",
        "categoria" => "Iluminação",
        "descricao" => "Peça de reposição para veículos com lanterna quebrada ou danificada.",
        "preco" => 260,
        "condicao" => "Recondicionada",
        "origem" => "Original",
        "imagem" => "lanterna.jpg",
        "compatibilidade" => "Confirmar lado esquerdo ou direito antes da instalação.",
        "observacao" => "Peça revisada, indicada para reposição com melhor custo-benefício."
    ],
    [
        "nome" => "Paralama",
        "categoria" => "Lataria",
        "descricao" => "Peça de lataria usada em reparos de colisão ou danos laterais.",
        "preco" => 390,
        "condicao" => "Novo",
        "origem" => "Paralela",
        "imagem" => "paralama.jpg",
        "compatibilidade" => "Compatível conforme modelo, ano e lado do veículo.",
        "observacao" => "Normalmente precisa de preparação e pintura antes da montagem."
    ]
];

function formatarPreco($valor) {
    if ($valor <= 0) {
        return "Sob consulta";
    }

    return "R$ " . number_format($valor, 2, ',', '.');
}

function validarPeca($peca) {
    if ($peca["nome"] == "" || $peca["preco"] < 0) {
        return false;
    }

    return true;
}
?>

<section class="section page-top catalog-page">
    <div class="container">

        <div class="section-title center">
            <span>Catálogo</span>
            <h1>Catálogo de peças automotivas</h1>
            <p>
                Consulte peças disponíveis para orçamento, troca, funilaria, pintura e montagem.
            </p>
        </div>

        <div class="catalog-search">
            <input 
                type="text" 
                id="catalogSearch" 
                placeholder="Pesquisar por peça, categoria, condição ou origem..."
            >
        </div>

        <div class="catalog-grid" id="catalogGrid">

            <?php foreach ($pecas as $peca): ?>
                <?php if (validarPeca($peca)): ?>

                    <article 
                        class="catalog-item"
                        data-search="<?php echo strtolower($peca['nome'] . ' ' . $peca['categoria'] . ' ' . $peca['condicao'] . ' ' . $peca['origem']); ?>"
                    >
                        <div class="catalog-card">

                            <div class="catalog-img">
                                <img src="assets/img/pecas/<?php echo $peca['imagem']; ?>" alt="<?php echo $peca['nome']; ?>">
                            </div>

                            <div class="catalog-body">

                                <div class="catalog-header">
                                    <span><?php echo $peca["categoria"]; ?></span>
                                    <strong><?php echo formatarPreco($peca["preco"]); ?></strong>
                                </div>

                                <h3><?php echo $peca["nome"]; ?></h3>

                                <p>
                                    <?php echo $peca["descricao"]; ?>
                                </p>

                                <div class="piece-tags">
                                    <span><?php echo $peca["condicao"]; ?></span>
                                    <span><?php echo $peca["origem"]; ?></span>
                                </div>

                                <button 
                                    type="button"
                                    class="btn-card btn-detalhes"
                                    data-nome="<?php echo htmlspecialchars($peca['nome']); ?>"
                                    data-categoria="<?php echo htmlspecialchars($peca['categoria']); ?>"
                                    data-preco="<?php echo htmlspecialchars(formatarPreco($peca['preco'])); ?>"
                                    data-condicao="<?php echo htmlspecialchars($peca['condicao']); ?>"
                                    data-origem="<?php echo htmlspecialchars($peca['origem']); ?>"
                                    data-descricao="<?php echo htmlspecialchars($peca['descricao']); ?>"
                                    data-compatibilidade="<?php echo htmlspecialchars($peca['compatibilidade']); ?>"
                                    data-observacao="<?php echo htmlspecialchars($peca['observacao']); ?>"
                                    data-imagem="assets/img/pecas/<?php echo htmlspecialchars($peca['imagem']); ?>"
                                >
                                    Ver detalhes
                                </button>

                            </div>

                        </div>
                    </article>

                <?php endif; ?>
            <?php endforeach; ?>

        </div>

        <div id="noResults" class="no-results">
            Nenhuma peça encontrada para esta pesquisa.
        </div>

    </div>
</section>

<div class="modal-peca" id="modalPeca">
    <div class="modal-conteudo">

        <button type="button" class="fechar-modal" id="fecharModal">
            ×
        </button>

        <div class="modal-img">
            <img src="" alt="Imagem da peça" id="modalImagem">
        </div>

        <span class="modal-categoria" id="modalCategoria"></span>

        <h2 id="modalNome"></h2>

        <p id="modalDescricao"></p>

        <div class="modal-info-grid">
            <div>
                <small>Preço</small>
                <strong id="modalPreco"></strong>
            </div>

            <div>
                <small>Condição</small>
                <strong id="modalCondicao"></strong>
            </div>

            <div>
                <small>Origem</small>
                <strong id="modalOrigem"></strong>
            </div>
        </div>

        <div class="modal-bloco">
            <h3>Compatibilidade</h3>
            <p id="modalCompatibilidade"></p>
        </div>

        <div class="modal-bloco">
            <h3>Observação</h3>
            <p id="modalObservacao"></p>
        </div>

        <a href="index.php?page=agendamento" class="btn-main">
            Solicitar orçamento
        </a>

    </div>
</div>