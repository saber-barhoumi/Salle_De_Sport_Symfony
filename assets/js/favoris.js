/*document.querySelectorAll('.add-to-favorites').forEach(button => {
    button.addEventListener('click', event => {
        event.preventDefault();
        const productId = button.getAttribute('data-product-id');

        fetch(`/ajouter-favoris/${productId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message); // Afficher la notification
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
    });*/
