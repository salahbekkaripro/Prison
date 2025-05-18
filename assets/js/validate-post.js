function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
  
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
  
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
  }
  
  function initValidatePostPage() {
    console.log("🟢 initValidatePostPage activé");
  
    const validateButtons = document.querySelectorAll('.validate-btn');
    const deleteButtons = document.querySelectorAll('.delete-btn');
  
    validateButtons.forEach(btn => {
      const clone = btn.cloneNode(true);
      btn.parentNode.replaceChild(clone, btn);
  
      clone.addEventListener('click', () => {
        const postId = clone.dataset.id;
        const card = document.getElementById('post-' + postId);
        if (!card) return;
  
        // Animation swipe gauche
        new Audio('/forum-prison/assets/sounds/swoosh.mp3').play().catch(() => {});
        card.classList.add('card-swipe-left');

        setTimeout(() => {
          fetch('../ajax/validate_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'post_id=' + encodeURIComponent(postId)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              card.remove();
              showToast("✅ Sujet validé !");
            } else {
              showToast("❌ Erreur lors de la validation.", "error");
            }
          })
          .catch(() => {
            showToast("❌ Erreur réseau lors de la validation.", "error");
          });
        }, 600);
      });
    });
  
    deleteButtons.forEach(btn => {
      const clone = btn.cloneNode(true);
      btn.parentNode.replaceChild(clone, btn);
  
      clone.addEventListener('click', () => {
        const postId = clone.dataset.id;
        const card = document.getElementById('post-' + postId);
        if (!card) return;
  
        if (!confirm('Supprimer définitivement ce sujet ?')) return;
  
        // Animation swipe droite
        new Audio('/forum-prison/assets/sounds/swoosh.mp3').play().catch(() => {});

        card.classList.add('card-swipe-right');
  
        setTimeout(() => {
          fetch('../ajax/delete_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'post_id=' + encodeURIComponent(postId)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              card.remove();
              showToast("🗑 Sujet supprimé !", "error");
            } else {
              showToast("❌ Erreur lors de la suppression.", "error");
            }
          })
          .catch(() => {
            showToast("❌ Erreur réseau lors de la suppression.", "error");
          });
        }, 600);
      });
    });
  }
  