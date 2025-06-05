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
  