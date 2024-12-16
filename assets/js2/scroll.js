
document.addEventListener('DOMContentLoaded', function () {
    if (window.location.hash) {
        const hash = window.location.hash;
        const targetId = hash.substring(1);
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            history.replaceState(null, null, ' '); // Supprime temporairement le hash

            setTimeout(() => {
                history.replaceState(null, null, hash); // Restaure le hash
                // Scroll fluide en insérant un délai
                window.scrollTo({
                    top: targetElement.offsetTop,
                    behavior: 'smooth'
                });
            }, 200); // Ajuster le délai avant le scroll si nécessaire
        }
    }
});
