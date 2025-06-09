// hotelBook.js - Interactivity for hotel booking page

document.addEventListener('DOMContentLoaded', function() {
    // Search form static alert
    const form = document.querySelector('.hotel-booking-form, .hotel-search-bar');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Search functionality coming soon!');
        });
    }

    // Book Now/View Place static alert
    const bookButtons = document.querySelectorAll('.hotel-card .btn');
    bookButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // If it's a link, let it go to hotelDetails.php
            if (btn.tagName !== 'A') {
                e.preventDefault();
                alert('Booking functionality coming soon!');
            }
        });
    });

    // Guest/Room selector logic
    const guestRoomBtn = document.getElementById('guestRoomBtn');
    const guestRoomDropdown = document.getElementById('guestRoomDropdown');
    const guestRoomSummary = document.getElementById('guestRoomSummary');
    const guestRoomPlaceholder = document.getElementById('guestRoomPlaceholder');
    let adult = 1, child = 0, room = 1;
    let dropdownOpen = false;

    function updateSummary() {
        let summary = `${adult} adult${adult > 1 ? 's' : ''}`;
        if (child > 0) {
            summary += `, ${child} child${child > 1 ? 'ren' : ''}`;
        }
        summary += `, ${room} room${room > 1 ? 's' : ''}`;
        if (guestRoomSummary) guestRoomSummary.textContent = summary;
    }

    function updateCounts() {
        document.getElementById('adultCount').textContent = adult;
        document.getElementById('childCount').textContent = child;
        document.getElementById('roomCount').textContent = room;
    }

    function updatePlaceholder() {
        if (dropdownOpen) {
            guestRoomPlaceholder.textContent = 'Guests/Rooms';
        } else {
            if (adult > 0 && room > 0) {
                let summary = `${adult} adult${adult > 1 ? 's' : ''}`;
                if (child > 0) {
                    summary += `, ${child} child${child > 1 ? 'ren' : ''}`;
                }
                summary += `, ${room} room${room > 1 ? 's' : ''}`;
                guestRoomPlaceholder.textContent = summary;
            } else {
                guestRoomPlaceholder.textContent = 'Guests/Rooms';
            }
        }
    }

    if (guestRoomBtn && guestRoomDropdown) {
        guestRoomBtn.addEventListener('click', function(e) {
            e.preventDefault();
            guestRoomDropdown.classList.toggle('show');
            dropdownOpen = guestRoomDropdown.classList.contains('show');
            updatePlaceholder();
        });

        guestRoomDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        document.addEventListener('click', function(e) {
            if (!guestRoomDropdown.contains(e.target) && !guestRoomBtn.contains(e.target)) {
                guestRoomDropdown.classList.remove('show');
                dropdownOpen = false;
                updatePlaceholder();
            }
        });

        document.getElementById('adultMinus').onclick = function() {
            if (adult > 1) adult--;
            updateCounts();
            updateSummary();
            updatePlaceholder();
        };
        document.getElementById('adultPlus').onclick = function() {
            adult++;
            updateCounts();
            updateSummary();
            updatePlaceholder();
        };
        document.getElementById('childMinus').onclick = function() {
            if (child > 0) child--;
            updateCounts();
            updateSummary();
            updatePlaceholder();
        };
        document.getElementById('childPlus').onclick = function() {
            child++;
            updateCounts();
            updateSummary();
            updatePlaceholder();
        };
        document.getElementById('roomMinus').onclick = function() {
            if (room > 1) room--;
            updateCounts();
            updateSummary();
            updatePlaceholder();
        };
        document.getElementById('roomPlus').onclick = function() {
            room++;
            updateCounts();
            updateSummary();
            updatePlaceholder();
        };
        updateCounts();
    }
    console.log('guestRoomBtn:', guestRoomBtn);
    console.log('guestRoomDropdown:', guestRoomDropdown);
}); 