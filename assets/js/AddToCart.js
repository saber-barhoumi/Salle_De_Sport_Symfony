$(document).ready(function() {
    // Gestion du clic sur le bouton "Ajouter au panier"
    $('.btn-ajouter-au-panier').on('click', function(e) {
        e.preventDefault();  // Empêche le comportement par défaut (soumission du formulaire, scroll de la page)

        // Récupérer l'ID du produit depuis l'attribut data-id
        var produitId = $(this).data('id');
        
        // Appel AJAX pour ajouter le produit au panier
        $.ajax({
            url: '/cart/add/' + produitId,  // L'URL de votre route Symfony
            type: 'GET',
            success: function(response) {
                // Si la requête est un succès, afficher un message
                if (response.success) {
                    alert(response.message);  // Affiche une alerte indiquant que le produit est ajouté

                    // Mettre à jour le nombre de produits dans le panier et le total
                    updateCartInfo(response.total);

                    // Optionnel: Ajouter un effet pour signaler l'ajout au panier (par exemple, un petit message en haut)
                    $("html, body").animate({ scrollTop: 0 }, "slow");  // Scroll vers le haut (si vous voulez scroller en haut après l'ajout)
                } else {
                    alert(response.message);  // Message d'erreur si le produit ne peut pas être ajouté
                }
            },
            error: function() {
                alert("Une erreur est survenue lors de l'ajout au panier.");
            }
        });
    });

    // Fonction pour mettre à jour les informations du panier (total, nombre d'articles, etc.)
    function updateCartInfo(total) {
        // Mettez à jour ici les informations du panier (comme le total, le nombre de produits, etc.)
        $('.panier-total').text("Total : " + total + "€");
    }
});
