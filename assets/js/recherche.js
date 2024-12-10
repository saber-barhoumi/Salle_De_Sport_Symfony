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

