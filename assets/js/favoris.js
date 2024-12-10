document.addEventListener('DOMContentLoaded', function () {
    const favorisContainer = document.getElementById('favoris-container');
    const reloadButton = document.getElementById('reload-favoris');

    function loadFavoris() {
        fetch('/favoris/liste') // Appel AJAX vers la route Symfony
            .then(response => response.json())
            .then(data => {
                favorisContainer.innerHTML = ''; // Effacer les favoris actuels

                if (data.favoris.length === 0) {
                    favorisContainer.innerHTML = '<p>Pas de favoris pour l\'instant.</p>';
                    return;
                }

                data.favoris.forEach(favori => {
                    const favorisItem = document.createElement('div');
                    favorisItem.classList.add('favori-item');
                    favorisItem.innerHTML = `
                        <h4>${favori.nom}</h4>
                        <img src="/uploads/images/${favori.image}" alt="${favori.nom}" style="width: 100px;">
                        <p>${favori.description}</p>
                        <p>Prix : ${favori.prix}€</p>
                    `;
                    favorisContainer.appendChild(favorisItem);
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des favoris :', error);
            });
    }

    // Charger les favoris au chargement de la page
    loadFavoris();

    // Recharger les favoris quand le bouton est cliqué
    reloadButton.addEventListener('click', loadFavoris);
});
