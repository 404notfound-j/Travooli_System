// Payment method selection
// Highlight the selected card when its radio is checked
const paymentRadios = document.querySelectorAll('.payment-method input[type="radio"]');
paymentRadios.forEach(radio => {
  radio.addEventListener('change', function() {
    document.querySelectorAll('.payment-method').forEach(label => label.classList.remove('selected'));
    this.closest('.payment-method').classList.add('selected');
    });
});

// Save card toggle
const toggle = document.querySelector('.toggle-switch');
toggle.addEventListener('click', function() {
    this.classList.toggle('active');
    document.getElementById('saveCardInput').value = this.classList.contains('active') ? '1' : '0';
});

// Simple form validation
document.querySelector('.confirm-btn').addEventListener('click', function(e) {
    const name = document.getElementById('cardName').value.trim();
    const number = document.getElementById('cardNumber').value.trim();
    const exp = document.getElementById('cardExp').value.trim();
    const cvv = document.getElementById('cardCVV').value.trim();
    if (!name || !number || !exp || !cvv) {
        alert('Please fill in all card details.');
        e.preventDefault();
    }
});
