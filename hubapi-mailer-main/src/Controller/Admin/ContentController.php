<?php

namespace SevenGroupFrance\SuluDuplicateArticlesBundle\Controller\Admin;

use Sulu\Bundle\ArticleBundle\Document\ArticleDocument;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Ramsey\Uuid\Uuid;

class ContentController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManagerInterface $documentManager) 
    {
        $this->documentManager = $documentManager;
    }

    // Duplique le contenu
    public function duplicateContent(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $requestData = json_decode($content, true);
        $contentId = $requestData['uid'];

        // Récupère le contenu à dupliquer
        $content = $this->documentManager->find($contentId, 'fr', ['load_ghost_content' => true]);

        // Vérifie si le contenu est une instance de ArticleDocument
        if (!$content instanceof ArticleDocument) {
            return new JsonResponse(['error' => 'Contenu introuvable.'], Response::HTTP_NOT_FOUND);
        }

        // Clone le contenu pour la duplication
        $duplicatedContent = clone $content;

        // Génère un nouvel UUID pour le contenu dupliqué
        $newUuid = Uuid::uuid4()->toString();
        $duplicatedContent->setUuid($newUuid);

        // Modifie le titre du contenu dupliqué
        $duplicatedContent->setTitle("Copie " . $duplicatedContent->getTitle());

        // Persiste le contenu dupliqué
        $this->documentManager->persist($duplicatedContent, "fr");
        $this->documentManager->flush();

        // Retourne la réponse JSON avec l'ID du contenu dupliqué
        return new JsonResponse(['id' => $duplicatedContent->getUuid()]);
    }
}
