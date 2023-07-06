import { translate } from 'sulu-admin-bundle/utils';
import { AbstractListToolbarAction } from 'sulu-admin-bundle/views';

export default class DuplicateContentAction extends AbstractListToolbarAction {
    // Récupère la configuration de l'élément de la barre d'outils
    getToolbarItemConfig() {
        const { disable_for_empty_selection: disableForEmptySelection = false } = this.options;

        return {
            type: 'button',
            label: translate('Dupliquer'), // Étiquette du bouton de duplication
            disabled: disableForEmptySelection && this.listStore.selections.length === 0, // Désactive le bouton si aucune sélection n'est faite
            onClick: this.handleClick, // Appelle la fonction handleClick lors du clic sur le bouton
            icon: 'su-copy', // Icône du bouton de duplication
        };
    }

    // Gère le clic sur l'élément de la barre d'outils
    handleClick = () => {
        if (this.listStore.selections.length !== 1) {
            alert("Veuillez choisir un seul contenu"); // Affiche une alerte si aucun ou plusieurs contenus sont sélectionnés
            return;
        }
        
        if(confirm("Voulez-vous dupliquer l'élément sélectionné ?")) { // Demande confirmation pour la duplication
            this.duplicateContent(); // Appelle la fonction de duplication du contenu
        }
    };

    // Duplique le contenu sélectionné
    duplicateContent = async () => {
        if (this.listStore.selections.length !== 1) {
            alert("Veuillez choisir un seul contenu"); // Affiche une alerte si aucun ou plusieurs contenus sont sélectionnés
            return;
        }

        const selectedContent = this.listStore.selections[0]; // Récupère le contenu sélectionné
        const contentId = selectedContent.id; // Récupère l'ID du contenu

        let params;
        params = {
            uid: contentId // Paramètres de la requête de duplication
        };

        const options = {
            method: 'POST', // Méthode de la requête
            body: JSON.stringify(params), // Corps de la requête (paramètres convertis en JSON)
            headers: {
                'Content-Type': 'application/json', // Type de contenu de la requête
            },
            redirect: 'follow', // Option de redirection de la requête
        };

        const url = '/admin/api/duplicate-content'; // URL de l'API de duplication de contenu
        
        fetch(url, options)
            .then((response) => {
                if (response.ok) {
                    window.location.reload(); // Recharge la page si la duplication est réussie
                } else {
                    throw new Error("Une erreur s'est produite lors de la duplication du contenu"); // Lance une erreur en cas d'échec de la duplication
                }
            })
            .catch((error) => {
                alert("Une erreur s'est produite :", error); // Affiche une alerte en cas d'erreur
            });
    };
}