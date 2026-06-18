document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("catalogSearch");
    const catalogItems = document.querySelectorAll(".catalog-item");
    const noResults = document.getElementById("noResults");

    if (searchInput && catalogItems.length > 0) {
        searchInput.addEventListener("input", function () {
            const searchValue = searchInput.value.toLowerCase().trim();
            let totalVisible = 0;

            catalogItems.forEach(function (item) {
                const itemText = item.getAttribute("data-search") || "";

                if (itemText.includes(searchValue)) {
                    item.style.display = "";
                    totalVisible++;
                } else {
                    item.style.display = "none";
                }
            });

            if (noResults) {
                noResults.style.display = totalVisible === 0 ? "block" : "none";
            }
        });
    }

    const modal = document.getElementById("modalPeca");
    const closeButton = document.getElementById("fecharModal");
    const detailButtons = document.querySelectorAll(".btn-detalhes");

    const modalNome = document.getElementById("modalNome");
    const modalCategoria = document.getElementById("modalCategoria");
    const modalPreco = document.getElementById("modalPreco");
    const modalCondicao = document.getElementById("modalCondicao");
    const modalOrigem = document.getElementById("modalOrigem");
    const modalDescricao = document.getElementById("modalDescricao");
    const modalCompatibilidade = document.getElementById("modalCompatibilidade");
    const modalObservacao = document.getElementById("modalObservacao");
    const modalImagem = document.getElementById("modalImagem");

    detailButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            if (!modal) {
                return;
            }

            modalNome.innerText = button.dataset.nome || "";
            modalCategoria.innerText = button.dataset.categoria || "";
            modalPreco.innerText = button.dataset.preco || "";
            modalCondicao.innerText = button.dataset.condicao || "";
            modalOrigem.innerText = button.dataset.origem || "";
            modalDescricao.innerText = button.dataset.descricao || "";
            modalCompatibilidade.innerText = button.dataset.compatibilidade || "";
            modalObservacao.innerText = button.dataset.observacao || "";

            if (modalImagem) {
                modalImagem.src = button.dataset.imagem || "";
                modalImagem.alt = button.dataset.nome || "Imagem da peça";
            }

            modal.classList.add("ativo");
            document.body.classList.add("modal-aberto");
        });
    });

    function fecharModal() {
        if (modal) {
            modal.classList.remove("ativo");
            document.body.classList.remove("modal-aberto");
        }
    }

    if (closeButton) {
        closeButton.addEventListener("click", fecharModal);
    }

    if (modal) {
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                fecharModal();
            }
        });
    }

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            fecharModal();
        }
    });
});