document.addEventListener('DOMContentLoaded', () => {
  let seatPrices = {}; // Cache seat prices per seat ID

  async function fetchSeatPrice(seatId) {
    if (seatPrices[seatId]) return seatPrices[seatId];
    try {
      const res = await fetch(`get_seat_price.php?flight_id=${flightId}&seat_id=${seatId}`);
      const data = await res.json();
      seatPrices[seatId] = data.price || 0;
      return seatPrices[seatId];
    } catch (error) {
      console.error(`Error fetching price for seat ${seatId}:`, error);
      return 0;
    }
  }

  async function updateTotal() {
    const selectedSeats = Array.from(document.querySelectorAll('.seat.selected'));
    let total = 0;

    for (const seat of selectedSeats) {
      const price = await fetchSeatPrice(seat.id);
      total += price;
    }

    const totalDisplay = document.getElementById('total-amount');
    if (totalDisplay) {
      totalDisplay.textContent = `RM ${total.toFixed(2)}`;
    }
    return total;
  }

  document.querySelectorAll('.seat').forEach(seat => {
    seat.addEventListener('click', async () => {
      if (seat.classList.contains('available')) {
        seat.classList.remove('available');
        seat.classList.add('selected');
      } else if (seat.classList.contains('selected')) {
        seat.classList.remove('selected');
        seat.classList.add('available');
      }
      await updateTotal();
    });
  });

  window.confirmSeats = async function() {
    const selectedSeats = Array.from(document.querySelectorAll('.seat.selected')).map(seat => seat.id);
    const totalPrice = await updateTotal();

    fetch(`seat_selection.php?action=save&flight_id=${flightId}&booking_id=${bookingId}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `seats=${selectedSeats.join(',')}&total_price=${totalPrice}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        window.location.href = `payment.php?booking_id=${bookingId}`;
      }
    });
  }

  fetch(`seat_selection.php?action=get&flight_id=${flightId}`)
    .then(res => res.json())
    .then(data => {
      data.forEach(seatId => {
        const seat = document.getElementById(seatId);
        if (seat) {
          seat.classList.remove('available');
          seat.classList.add('unavailable');
        }
      });
    });
});
