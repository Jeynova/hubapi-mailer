<?php

declare(strict_types=1);

namespace SevenGroupFrance\SuluDuplicateArticlesBundle\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ListViewBuilderInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\ArticleBundle\Admin\ArticleAdmin;

class AppAdmin extends Admin
{
    public function configureViews(ViewCollection $viewCollection): void
    {
        // Récupère la liste des contenus à dupliquer depuis la variable d'environnement
        $duplicateContentList = $_ENV["DUPLICATE_CONTENT_LIST"];
        $contentArray = explode(',', $duplicateContentList);

        // Parcourt chaque contenu à dupliquer
        foreach ($contentArray as $content) {
            // Vérifie si la vue du contenu existe dans la collection de vues
            if ($viewCollection->has('sulu_article.list_'.$content)) {
                /** @var ListViewBuilderInterface $contentListViewBuilder */
                $contentListViewBuilder = $viewCollection->get('sulu_article.list_'.$content);

                // Ajoute l'action de la barre d'outils pour la duplication
                $contentListViewBuilder->addToolbarActions([
                    new ToolbarAction('Dupliquer', ['disable_for_empty_selection' => true]),
                ]);
            }
        }
    }

    public static function getPriority(): int
    {
        return ArticleAdmin::getPriority() - 1;
    }
}