document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.hotel-details-header-actions .hotel-card-fav-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            btn.classList.toggle('selected');
        });
    });
}); 