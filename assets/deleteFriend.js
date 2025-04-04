document.addEventListener('DOMContentLoaded', () => {
    //Récupérer tous les deleteButton 
    const deleteButtons = document.querySelectorAll('.deleteButton');
    //Pour chacun, exécuter deleteClick au click
    deleteButtons.forEach(deleteButton => {
        deleteButton.addEventListener('click', deleteClick);
    });

    function deleteClick(event) {
        //Bloquer la redirection 
        event.preventDefault();

        //Demander confirmation à l'utilisateur
        if (confirm("Supprimer cet ami lui retire l'accès à vos fichiers. Êtes-vous sûr de vouloir le supprimer ?")) {
            //S'il confirme, on récupère le lien href du deleteButton pour recharger la page avec la suppression
            window.location.href = event.currentTarget
        }
    }

    //Récupérer tous les declineButton
    const declineButtons = document.querySelectorAll('.declineButton');
    //Pour chacun, exécuter declineClick au click
    declineButtons.forEach(declineButton => {
        declineButton.addEventListener('click', declineClick);
    });

    function declineClick(event) {
        //Bloquer la redirection 
        event.preventDefault();

        //Demander confirmation à l'utilisateur
        if (confirm("Refuser cette amitié empêchera tout partage de fichiers avec cet ami. Êtes-vous sûr de vouloir refuser ?")) {
            //S'il confirme, on récupère le lien href du declineButton pour recharger la page avec le refus
            window.location.href = event.currentTarget;
        }
    }
});