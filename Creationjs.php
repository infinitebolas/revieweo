<script>
    document.addEventListener('DOMContentLoaded', () => {
    // Gestion de la soumission du formulaire via AJAX
    const form = document.getElementById('creationForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Empêche le rechargement de la page

        const formData = new FormData(form);

        fetch('create_critique.php', { // Nouveau nom du fichier
            method: 'POST',
            body: formData
        })
        .then(resp => resp.json())
        .then(data => {
            const messageDiv = document.getElementById('message');
            if (data.success) {
                messageDiv.innerHTML = `<div class="alert alert-success">${data.success}</div>`;
                form.reset(); // Réinitialiser le formulaire
            } else if (data.error) {
                messageDiv.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        })
        .catch(err => {
            console.error('Erreur lors de l\'envoi du formulaire :', err);
            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = `<div class="alert alert-danger">Erreur réseau ou serveur.</div>`;
        });
    });
});
</script>