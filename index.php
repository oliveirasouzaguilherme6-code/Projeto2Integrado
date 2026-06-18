<?php
$page = $_GET['page'] ?? 'home';

$allowedPages = ['home', 'servicos', 'pacotes', 'catalogo', 'agendamento', 'contato'];

if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

$pagePath = "pages/{$page}.php";
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<main>
    <?php
        if (file_exists($pagePath)) {
            include $pagePath;
        } else {
            echo "<section class='secao'><div class='container'><h1>Página em construção</h1><p>Esta página ainda será criada.</p></div></section>";
        }
    ?>
</main>

<?php include 'includes/footer.php'; ?>