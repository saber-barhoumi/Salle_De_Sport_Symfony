import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
document.querySelector('.favoris-icon').addEventListener('click', function () {
    fetch('/favoris/liste')
        .then(response => response.json())
        .then(data => {
            console.log(data); // Vérifiez si les données sont correctes.
            // Mettez à jour l'interface utilisateur avec les favoris
            const favorisContainer = document.querySelector('#favoris-container');
            favorisContainer.innerHTML = data.favoris.map(favori => `
                <div>
                    <img src="${favori.image}" alt="${favori.nom}" />
                    <p>${favori.nom} - ${favori.prix}€</p>
                </div>
            `).join('');
        })
        .catch(error => console.error('Erreur:', error));
});
