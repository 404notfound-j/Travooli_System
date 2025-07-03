document.addEventListener("DOMContentLoaded", function () {
  const total = sessionStorage.getItem('total_price') || '0';
  const ticket = sessionStorage.getItem('ticket_price') || '0';
  const baggage = sessionStorage.getItem('baggage_price') || '0';
  const meal = sessionStorage.getItem('meal_price') || '0';
  const label1 = sessionStorage.getItem('ticket_label') || 'Tickets';
  const adults = parseInt(sessionStorage.getItem('adultCount')) || 1;
  const children = parseInt(sessionStorage.getItem('childCount')) || 0;

  // Update the prices
  document.getElementById('flight-price').textContent = `RM ${ticket}`;
  document.querySelector('.meal-price').textContent = `RM ${meal}`;
  document.querySelector('.baggage-price').textContent = `RM ${baggage}`;
  document.getElementById('total').textContent = `RM ${total}`;
  document.getElementById('ticket-count-label').textContent = label1;
  // Generate dynamic passenger label
  let label = 'Tickets (';
  if (adults > 0) label += `${adults} Adult${adults > 1 ? 's' : ''}`;
  if (children > 0) {
    label += adults > 0 ? ', ' : '';
    label += `${children} Child${children > 1 ? 'ren' : ''}`;
  }
  label += ')';

  document.getElementById('ticket-count-label').textContent = label;
});

document.querySelectorAll('.seat').forEach(seat => {
    seat.addEventListener('click', () => {
      if (seat.classList.contains('available')) {
        seat.classList.remove('available');
        seat.classList.add('selected');
      } else if (seat.classList.contains('selected')) {
        seat.classList.remove('selected');
        seat.classList.add('available');
      }
    });
  });
  