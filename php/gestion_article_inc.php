<?php

require_once 'db_article.inc.php';

use Blog\ArticleRepository;
use Blog\Article;

/**
 * Récupère la liste des articles avec un message d'état.
 *
 * @return array{articles: array<\Blog\Article>, message: string} Tableau associatif contenant les articles et le message
 */
function getListArticles(): array
{
    $message = '';

    $articleRepository = new ArticleRepository();
    $articles = $articleRepository->getAllArticles($message);
    $message .= (count($articles) < 1) ? 'Pas d\' article pour le moment' : '';

    return [
        'articles' => $articles,
        'message'  => $message
    ];
}



/**
 * Récupère un article par son identifiant avec un message d'état.
 *
 * @param int|null $idArticle Identifiant de l'article 
 * @return array{article: \Blog\Article|null, message: string} Article trouvé (ou null) et message d'état
 */
function getOneArticle(?int $idArticle): array
{
    $message = '';
    $article = null;

    if ($idArticle === null || $idArticle === false) {
        $message = "Erreur : L'identifiant de l'article est invalide.";
    } else {
        $articleRepository = new ArticleRepository();
        $article = $articleRepository->getArticleById($idArticle, $message);

        if (!$article) {
            $message .= "Erreur : L'article demandé n'existe pas.";
        }
    }

    return [
        'article' => $article,
        'message' => $message
    ];
}


/**
 * Supprime un article par son identifiant et retourne la liste mise à jour avec les messages.
 *
 * @param int $idArticle Identifiant de l'article à supprimer
 * @return array{
 *     articles: array<\Blog\Article>,
 *     message: string,
 *     messageErreur: string
 * }
 */
function validateAndDeleteArticleById(int $idArticle): array
{

    $messageErreur =  $message = '';
    $articles = [];

    $articleRepository = new ArticleRepository();

    if ($articleRepository->deleteArticle($idArticle, $messageErreur)) {
        $message = "L'article a bien été supprimé.";
        ['articles' => $articles, 'message' => $messageList] = getListArticles();
        $messageErreur .= $messageList ? " ($messageList)" : '';
    } else {
        $messageErreur = "Erreur lors de la suppression de l'article.";
    }

    return [
        'articles' => $articles,
        'message' => $message,
        'messageErreur' => $messageErreur
    ];
}


/**
 * Validation des données de l'article
 * 
 * @param string $titre Titre de l'article
 * @param string $contenu Contenu de l'article
 * @return array Tableau des erreurs
 */
function validateArticle(string $titre, string $contenu): array
{
    $errors = [];
    if (empty($titre)) {
        $errors[] = 'Le titre ne peut pas être vide';
    } elseif (mb_strlen($titre) > 100) {
        $errors[] = 'Le titre ne peut pas excéder 100 caractères.';
    }
    if (empty($contenu)) {
        $errors[] = 'Le contenu ne peut pas être vide';
    }
    return $errors;
}


function processUpdateOrInsert($id, $titre, $contenu)
{

    $message = $messageErreur = '';
    $erreurs = [];
    $article = new Article();
    $article->titre = $titre;
    $article->contenu = $contenu;

    $articleRepository = new ArticleRepository();

    if ($id !== false && $id !== null) {
        // Mode modification
        $article->id = $id;
        if ($articleRepository->updateArticle($article, $messageErreur)) {
            $message = "Article mis à jour avec succès.";
        } else {
            $erreurs[] = "Erreur technique lors de la mise à jour.";
            $erreurs[] = $messageErreur;
        }
    } else {
        // Mode ajout
        if ($articleRepository->insertArticle($article, $messageErreur)) {
            $message .= "Article correctement ajouté.";
            $titre = $contenu = '';
        } else {
            $erreurs[]  = "Erreur technique. Veuillez contacter l'administrateur.";
            $erreurs[] = $messageErreur;
        }
    }

    return [
        'messageErreur' => $messageErreur,
        'message' => $message,
        'erreurs' => $erreurs
    ];
}

/**
 * Gère le traitement du formulaire d'article (ajout ou modification).
 *
 * @param array $post Données du formulaire ($_POST)
 * @return array{
 *     titre: string,
 *     contenu: string,
 *     id: int|null,
 *     erreurs: array<string>,
 *     message: string
 * }
 */
function handleArticleForm(array $post): array
{
    $titre = nettoyage($post['titre'] ?? '');
    $contenu = nettoyage($post['contenu'] ?? '');
    $id = isset($post['id']) ? filter_var($post['id'], FILTER_VALIDATE_INT) : null;

    $erreurs = validateArticle($titre, $contenu);
    $message = '';

    if (empty($erreurs)) {
        ['erreurs' => $erreurs, 'message' => $message] = processUpdateOrInsert($id, $titre, $contenu);
    }

    return [
        'titre' => $titre,
        'contenu' => $contenu,
        'id' => $id,
        'erreurs' => $erreurs,
        'message' => $message
    ];
}
