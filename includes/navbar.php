<header class="topo-site">
    <nav class="navbar navbar-expand-lg">
        <div class="container">

            <a class="navbar-brand marca" href="index.php?page=home">
                <img src="assets/img/logo-mm.png" alt="Logo M&M" class="logo-site">

                <div class="marca-texto">
                    <strong>M&M</strong>
                    <span>Centro Estético Automotivo</span>
                </div>
            </a>

            <button class="navbar-toggler botao-menu" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 menu-links">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'home' ? 'ativo' : ''; ?>" href="index.php?page=home">Início</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'servicos' ? 'ativo' : ''; ?>" href="index.php?page=servicos">Serviços</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'pacotes' ? 'ativo' : ''; ?>" href="index.php?page=pacotes">Pacotes</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'catalogo' ? 'ativo' : ''; ?>" href="index.php?page=catalogo">Catálogo</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'agendamento' ? 'ativo' : ''; ?>" href="index.php?page=agendamento">Agendamento</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'contato' ? 'ativo' : ''; ?>" href="index.php?page=contato">Contato</a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
</header>