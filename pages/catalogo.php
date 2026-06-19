<?php
require_once __DIR__ . "/../config/database.php";

function formatarPreco($valor) {
    if ($valor <= 0) {
        return "Sob consulta";
    }

    return "R$ " . number_format($valor, 2, ',', '.');
}

try {
    $sql = "SELECT * FROM pecas WHERE ativo = 1 ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $erro) {
    die("Erro ao buscar peças: " . $erro->getMessage());
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

            <?php if (count($pecas) > 0): ?>

                <?php foreach ($pecas as $peca): ?>

                    <article 
                        class="catalog-item"
                        data-search="<?php echo strtolower($peca['nome'] . ' ' . $peca['categoria'] . ' ' . $peca['condicao'] . ' ' . $peca['origem']); ?>"
                    >
                        <div class="catalog-card">

                            <div class="catalog-img">
                                <img 
                                    src="assets/img/pecas/<?php echo htmlspecialchars($peca['imagem']); ?>" 
                                    alt="<?php echo htmlspecialchars($peca['nome']); ?>"
                                >
                            </div>

                            <div class="catalog-body">

                                <div class="catalog-header">
                                    <span><?php echo htmlspecialchars($peca["categoria"]); ?></span>
                                    <strong><?php echo formatarPreco($peca["preco"]); ?></strong>
                                </div>

                                <h3><?php echo htmlspecialchars($peca["nome"]); ?></h3>

                                <p>
                                    <?php echo htmlspecialchars($peca["descricao"]); ?>
                                </p>

                                <div class="piece-tags">
                                    <span><?php echo htmlspecialchars($peca["condicao"]); ?></span>
                                    <span><?php echo htmlspecialchars($peca["origem"]); ?></span>
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

                <?php endforeach; ?>

            <?php else: ?>

                <div class="no-results" style="display:block;">
                    Nenhuma peça cadastrada no banco de dados.
                </div>

            <?php endif; ?>

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