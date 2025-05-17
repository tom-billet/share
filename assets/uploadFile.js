document.addEventListener('DOMContentLoaded', () => {
    const uploadBtns = document.querySelectorAll('#file_user_ajouter');
    uploadBtns.forEach(uploadBtn => {
        uploadBtn.addEventListener('click', uploadClick);
    });

    function uploadClick(event) {

        event.preventDefault();
        const confirmed = confirm("Votre fichier sera téléchargé vers nos serveurs conformément à nos conditions d'utilisation. Tout contenu illégal est interdit au téléchargement.");
        
        if(!confirmed) {
            event.preventDefault;
        }
    }
});