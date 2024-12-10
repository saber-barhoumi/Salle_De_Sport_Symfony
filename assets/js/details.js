document.addEventListener('DOMContentLoaded', function () {
    const modalContent = document.getElementById('productModalContent');

    document.querySelectorAll('.btn-details').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.id;

            fetch(`/produit/${productId}/modal`)
                .then(response => response.text())
                .then(html => {
                    modalContent.innerHTML = html; // Charger le contenu du modal
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des détails :', error);
                    modalContent.innerHTML = '<p>Erreur lors du chargement des détails.</p>';
                });
        });
    });
});
