// public/js/recherche.js
document.querySelector('.search-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Empêche le rechargement de la page

    const formData = new FormData(this);

    fetch('/rechercher', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.text())
    .then(html => {
        // Met à jour le contenu du modal avec les résultats
        document.querySelector('#resultModal .modal-body .product-results').innerHTML = html;

        // Affiche le modal
        const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
        resultModal.show();
    })
    .catch(error => console.error('Erreur:', error));
});











// Fonction pour afficher un message de confirmation quand un produit est ajouté au panier
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const button = this.querySelector('.add-to-cart-btn');
        button.innerHTML = 'Ajouté !';
        button.style.backgroundColor = '#2ecc71';

        // Restauration de l'état du bouton après 1 seconde
        setTimeout(() => {
            button.innerHTML = 'Ajouter au Panier';
            button.style.backgroundColor = '#3498db';
        }, 1000);
    });
});

// Animation du panier pour attirer l'attention sur le lien "Voir Mon Panier"
const cartLink = document.querySelector('.cart-link');
cartLink.addEventListener('mouseover', function() {
    cartLink.style.transform = 'scale(1.1)';
    cartLink.style.transition = 'transform 0.3s ease';
});

cartLink.addEventListener('mouseout', function() {
    cartLink.style.transform = 'scale(1)';
});
