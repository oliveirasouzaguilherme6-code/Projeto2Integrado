<header class="site-header">
  

    <nav class="navbar navbar-expand-lg">
        <div class="container">

            <a class="navbar-brand brand" href="index.php?page=home">
                <div class="brand-logo-area">
                    <img src="assets/img/logo-mm.jpeg" alt="Logo M&M" class="brand-logo">
                </div>

                <div class="brand-text">
                    <strong>M&M</strong>
                    <span>Centro Estético Automotivo</span>
                </div>
            </a>

            <button 
                class="navbar-toggler menu-button" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#menuSite"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuSite">
                <ul class="navbar-nav ms-auto site-menu">

                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'home' ? 'active-page' : ''; ?>" href="index.php?page=home">Início</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'servicos' ? 'active-page' : ''; ?>" href="index.php?page=servicos">Serviços</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'catalogo' ? 'active-page' : ''; ?>" href="index.php?page=catalogo">Catálogo</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'agendamento' ? 'active-page' : ''; ?>" href="index.php?page=agendamento">Agendamento</a>
                    </li>

                </ul>
            </div>

        </div>
    </nav>
</header>