<?php

require_once  '../php/config_perso.inc.php';
require_once  '../php/utils.inc.php';
require_once  '../php/gestion_article_inc.php';

$messageErreur = "";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

//obtenir un article
['article' => $article, 'message' => $messageErreur] = getOneArticle($id);


?>

<?php include  '../inc/head.inc.php' ?>
<?php include  '../inc/header.inc.php' ?>

<main class="centrage boxOmbre">
    <ul class="containerFlex">
        <li><i class="fa fa-arrow-left"></i> <a href="<?= BASE_URL ?>"> vers la liste des articles</a></li>
    </ul>
    <?php if (isset($article) && $article) { ?>
        <h1><?= nettoyage($article->titre) ?></h1>

        <p class="basis50"><?= nl2br(nettoyage($article->contenu)) ?></p>


    <?php } else {
        afficherAlerte($messageErreur, 'danger');
    } ?>


</main>


<?php include '../inc/footer.inc.php' ?>