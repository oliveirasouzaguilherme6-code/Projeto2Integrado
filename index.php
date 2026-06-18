<?php
$page = $_GET['page'] ?? 'home';

$allowedPages = ['home', 'servicos', 'catalogo', 'agendamento'];

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
            include 'pages/home.php';
        }
    ?>
</main>

<?php include 'includes/footer.php'; ?>