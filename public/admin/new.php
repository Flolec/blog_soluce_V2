<?php
require_once  '../../php/config_perso.inc.php';
require  '../../php/utils.inc.php';
require  '../../php/gestion_article_inc.php';

// Récupération de l'ID en GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$titre = $contenu  = '';
$erreurs = [];
$messageErreur = $message  = '';


//obtenir un article
['article' => $article, 'message' => $messageErreur] = getOneArticle($id);

if ($article) {
    $titre = nettoyage($article->titre);
    $contenu = nettoyage($article->contenu);
}

//soumission du formulaire
if (isset($_POST['btn_article'])) {
    /* $result = handleArticleForm($_POST);
    $titre = $result['titre'];
    $contenu = $result['contenu'];
    $id = $result['id'];
    $erreurs = $result['erreurs'];
    $message = $result['message'];
    */

    extract(handleArticleForm($_POST));
}

?>


<?php include   '../../inc/head.inc.php' ?>
<?php include   '../../inc/header.inc.php' ?>

<main class="centrage boxOmbre">

    <h1><?= $id ? 'Modifier' : 'Nouvel' ?> Article</h1>
    <ul class="containerFlex">
        <li><i class="fa fa-arrow-left"></i> <a href="<?= BASE_URL ?>"> vers la liste des articles</a></li>
    </ul>
    <form action="new.php" method="POST" class="formAdmin">
        <h2><?= $id ? 'Modifier' : 'Ajouter' ?> un article</h2>
        <?php
        afficherAlerte($message, 'success');
        afficherAlerte($erreurs, 'danger');
        ?>

        <input type="hidden" name="id" value="<?= $id ?>">

        <!-- Pour tester, les attributs required ont été enlevés et autres validations maxlength="100" -->
        <label for="titre">Titre *<br><small>100 caractères max</small></label><input type="text" size="50" id="titre" name="titre" value="<?= $titre ?>">
        <label for="contenu">Contenu *</label><textarea name="contenu" id="contenu"><?= $contenu ?></textarea>
        <input type="submit" class="btn btn-theme" name="btn_article" value="<?= $id ? 'Modifier' : 'Ajouter' ?>">

    </form>
</main>


<?php include  '../../inc/footer.inc.php' ?>