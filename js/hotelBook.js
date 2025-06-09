// hotelBook.js - Interactivity for hotel booking page

// Global variables for guest room selector
let adult = 1, child = 0, room = 1;
let dropdownOpen = false;

// Global variables for hotel date picker
let hotelCurrentDate = new Date();
let selectedCheckinDate = null;
let selectedCheckoutDate = null;
let isSelectingCheckout = false;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeStaticAlerts();
    initializeHotelDatePicker();
    initializeGuestRoomSelector();
    initializeFavoriteButtons();
});

// Consolidated static alerts
function initializeStaticAlerts() {
    // Search form alert
    const form = document.querySelector('.hotel-booking-form, .hotel-search-bar');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Search functionality coming soon!');
        });
    }

    // Book buttons alert (exclude actual links)
    const bookButtons = document.querySelectorAll('.hotel-card .btn');
    bookButtons.forEach(btn => {
        if (btn.tagName !== 'A') {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Booking functionality coming soon!');
            });
        }
    });
}

// Guest Room Selector
function initializeGuestRoomSelector() {
    const guestRoomBtn = document.getElementById('guestRoomBtn');
    const guestRoomDropdown = document.getElementById('guestRoomDropdown');

    if (guestRoomBtn && guestRoomDropdown) {
        // Toggle dropdown on button click
        guestRoomBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            hideHotelDatePicker(); // Close other dropdowns
            
            const isCurrentlyOpen = guestRoomDropdown.classList.contains('show');
            if (isCurrentlyOpen) {
                hideGuestRoomDropdown();
            } else {
                showGuestRoomDropdown();
            }
        });

        // Prevent dropdown from closing when clicking inside
        guestRoomDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!guestRoomDropdown.contains(e.target) && !guestRoomBtn.contains(e.target)) {
                hideGuestRoomDropdown();
            }
        });

        // Initialize counter buttons and update display
        initializeGuestCounterButtons();
        updateGuestDisplay();
    }
}

function showGuestRoomDropdown() {
    const guestRoomDropdown = document.getElementById('guestRoomDropdown');
    const guestRoomSelector = document.getElementById('guestRoomSelector');
    
    guestRoomDropdown.classList.add('show');
    guestRoomDropdown.style.display = 'block';
    guestRoomSelector.classList.add('active');
    dropdownOpen = true;
}

function hideGuestRoomDropdown() {
    const guestRoomDropdown = document.getElementById('guestRoomDropdown');
    const guestRoomSelector = document.getElementById('guestRoomSelector');
    
    guestRoomDropdown.classList.remove('show');
    guestRoomDropdown.style.display = 'none';
    guestRoomSelector.classList.remove('active');
    dropdownOpen = false;
    
    updateGuestPlaceholder();
}

// Consolidated guest display update function
function updateGuestDisplay() {
    // Update counter displays
    const elements = {
        adult: document.getElementById('hotelAdultCount'),
        child: document.getElementById('hotelChildCount'),
        room: document.getElementById('hotelRoomCount')
    };
    
    if (elements.adult) elements.adult.textContent = adult;
    if (elements.child) elements.child.textContent = child;
    if (elements.room) elements.room.textContent = room;
    
    // Update placeholder
    updateGuestPlaceholder();
}

function updateGuestPlaceholder() {
    const guestRoomPlaceholder = document.getElementById('guestRoomPlaceholder');
    
    if (guestRoomPlaceholder) {
        let summary = `${adult} adult${adult > 1 ? 's' : ''}`;
        if (child > 0) {
            summary += `, ${child} child${child > 1 ? 'ren' : ''}`;
        }
        summary += `, ${room} room${room > 1 ? 's' : ''}`;
        guestRoomPlaceholder.textContent = summary;
    }
}

function initializeGuestCounterButtons() {
    const counterButtons = document.querySelectorAll('.guest-counter-btn');
    
    counterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const type = this.dataset.type;
            const isPlus = this.classList.contains('plus');
            const isMinus = this.classList.contains('minus');
            
            // Update counters based on type and action
            if (type === 'adult') {
                if (isPlus && adult < 9) adult++;
                else if (isMinus && adult > 1) adult--;
            } else if (type === 'child') {
                if (isPlus && child < 9) child++;
                else if (isMinus && child > 0) child--;
            } else if (type === 'room') {
                if (isPlus && room < 9) room++;
                else if (isMinus && room > 1) room--;
            }
            
            updateGuestDisplay();
            updateGuestCounterButtonStates();
        });
    });
    
    updateGuestCounterButtonStates();
}

function updateGuestCounterButtonStates() {
    const buttons = {
        adultMinus: document.querySelector('.guest-counter-btn.minus[data-type="adult"]'),
        adultPlus: document.querySelector('.guest-counter-btn.plus[data-type="adult"]'),
        childMinus: document.querySelector('.guest-counter-btn.minus[data-type="child"]'),
        childPlus: document.querySelector('.guest-counter-btn.plus[data-type="child"]'),
        roomMinus: document.querySelector('.guest-counter-btn.minus[data-type="room"]'),
        roomPlus: document.querySelector('.guest-counter-btn.plus[data-type="room"]')
    };
    
    if (buttons.adultMinus) buttons.adultMinus.classList.toggle('disabled', adult <= 1);
    if (buttons.adultPlus) buttons.adultPlus.classList.toggle('disabled', adult >= 9);
    if (buttons.childMinus) buttons.childMinus.classList.toggle('disabled', child <= 0);
    if (buttons.childPlus) buttons.childPlus.classList.toggle('disabled', child >= 9);
    if (buttons.roomMinus) buttons.roomMinus.classList.toggle('disabled', room <= 1);
    if (buttons.roomPlus) buttons.roomPlus.classList.toggle('disabled', room >= 9);
}

// Hotel Date Picker
function initializeHotelDatePicker() {
    const hotelDateInput = document.getElementById('hotelDateInput');
    const hotelDatePickerDropdown = document.getElementById('hotelDatePickerDropdown');
    
    if (!hotelDateInput || !hotelDatePickerDropdown) return;
    
    generateHotelCalendar();
    
    // Main date input click
    hotelDateInput.addEventListener('click', function(e) {
        e.stopPropagation();
        closeAllDropdowns();
        showHotelDatePicker();
    });
    
    // Date field clicks
    const checkinDateInput = document.getElementById('checkinDate');
    const checkoutDateInput = document.getElementById('checkoutDate');
    
    if (checkinDateInput) {
        checkinDateInput.addEventListener('click', function(e) {
            e.stopPropagation();
            setDateSelectionMode(false);
        });
    }
    
    if (checkoutDateInput) {
        checkoutDateInput.addEventListener('click', function(e) {
            e.stopPropagation();
            setDateSelectionMode(true);
        });
    }
    
    // Navigation buttons
    const prevMonthBtn = document.querySelector('.hotel-prev-month');
    const nextMonthBtn = document.querySelector('.hotel-next-month');
    
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            hotelCurrentDate.setMonth(hotelCurrentDate.getMonth() - 1);
            generateHotelCalendar();
        });
    }
    
    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            hotelCurrentDate.setMonth(hotelCurrentDate.getMonth() + 1);
            generateHotelCalendar();
        });
    }
    
    // Done button
    const doneBtn = document.getElementById('hotelDateDone');
    if (doneBtn) {
        doneBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (selectedCheckinDate) {
                updateHotelDateDisplay();
                hideHotelDatePicker();
            }
        });
    }
    
    // Prevent dropdown from closing when clicking inside
    hotelDatePickerDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Close when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.hotel-date-picker-wrapper')) {
            hideHotelDatePicker();
        }
    });
}

function setDateSelectionMode(isCheckout) {
    isSelectingCheckout = isCheckout;
    const checkinField = document.getElementById('checkinField');
    const checkoutField = document.getElementById('checkoutField');
    
    if (isCheckout) {
        checkoutField.classList.add('focused');
        checkinField.classList.remove('focused');
    } else {
        checkinField.classList.add('focused');
        checkoutField.classList.remove('focused');
    }
    
    generateHotelCalendar();
}

function showHotelDatePicker() {
    const hotelDatePickerDropdown = document.getElementById('hotelDatePickerDropdown');
    const hotelDatePickerWrapper = document.querySelector('.hotel-date-picker-wrapper');
    
    hotelDatePickerDropdown.classList.add('show');
    hotelDatePickerWrapper.classList.add('active');
    generateHotelCalendar();
}

function hideHotelDatePicker() {
    const hotelDatePickerDropdown = document.getElementById('hotelDatePickerDropdown');
    const hotelDatePickerWrapper = document.querySelector('.hotel-date-picker-wrapper');
    
    hotelDatePickerDropdown.classList.remove('show');
    hotelDatePickerWrapper.classList.remove('active');
}

function generateHotelCalendar() {
    const month1 = new Date(hotelCurrentDate.getFullYear(), hotelCurrentDate.getMonth(), 1);
    const month2 = new Date(hotelCurrentDate.getFullYear(), hotelCurrentDate.getMonth() + 1, 1);
    
    // Update month headers
    const month1Header = document.getElementById('hotelCurrentMonth1');
    const month2Header = document.getElementById('hotelCurrentMonth2');
    
    if (month1Header) month1Header.textContent = formatMonthYear(month1);
    if (month2Header) month2Header.textContent = formatMonthYear(month2);
    
    // Generate calendars
    generateHotelMonthCalendar(month1, 'hotelCalendarDates1');
    generateHotelMonthCalendar(month2, 'hotelCalendarDates2');
}

function generateHotelMonthCalendar(date, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();
    const today = new Date();
    
    container.innerHTML = '';
    let dayCount = 1;
    
    // Generate calendar grid (6 weeks)
    for (let week = 0; week < 6; week++) {
        const weekRow = document.createElement('div');
        weekRow.className = 'hotel-calendar-row';
        
        for (let day = 0; day < 7; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'hotel-calendar-date';
            const dayIndex = week * 7 + day;
            
            if (dayIndex < startingDayOfWeek) {
                // Previous month days
                const prevMonth = new Date(year, month - 1, 0);
                const prevDay = prevMonth.getDate() - (startingDayOfWeek - dayIndex - 1);
                dayElement.textContent = prevDay;
                dayElement.classList.add('adjacent-month');
            } else if (dayCount <= daysInMonth) {
                // Current month days
                const currentDay = new Date(year, month, dayCount);
                dayElement.textContent = dayCount;
                
                if (currentDay >= today) {
                    dayElement.classList.add('available');
                    dayElement.addEventListener('click', () => selectHotelDate(currentDay));
                } else {
                    dayElement.classList.add('past');
                }
                
                // Highlight selected dates and ranges
                if (selectedCheckinDate && isSameDate(currentDay, selectedCheckinDate)) {
                    dayElement.classList.add('selected');
                }
                if (selectedCheckoutDate && isSameDate(currentDay, selectedCheckoutDate)) {
                    dayElement.classList.add('selected');
                }
                if (selectedCheckinDate && selectedCheckoutDate && 
                    currentDay > selectedCheckinDate && currentDay < selectedCheckoutDate) {
                    dayElement.classList.add('in-range');
                }
                
                dayCount++;
            } else {
                // Next month days
                const nextDay = dayCount - daysInMonth;
                dayElement.textContent = nextDay;
                dayElement.classList.add('adjacent-month');
                dayCount++;
            }
            
            weekRow.appendChild(dayElement);
        }
        
        container.appendChild(weekRow);
    }
}

function selectHotelDate(date) {
    if (!isSelectingCheckout) {
        selectedCheckinDate = new Date(date);
        setDateSelectionMode(true);
        
        // Clear checkout date if it's before new checkin date
        if (selectedCheckoutDate && selectedCheckoutDate <= selectedCheckinDate) {
            selectedCheckoutDate = null;
        }
    } else {
        // Only allow checkout date after checkin date
        if (date > selectedCheckinDate) {
            selectedCheckoutDate = new Date(date);
            setDateSelectionMode(false);
        }
    }
    
    updateHotelDateFieldDisplay();
    generateHotelCalendar();
}

function updateHotelDateFieldDisplay() {
    const checkinDateInput = document.getElementById('checkinDate');
    const checkoutDateInput = document.getElementById('checkoutDate');
    
    if (checkinDateInput && selectedCheckinDate) {
        checkinDateInput.value = formatDisplayDate(selectedCheckinDate);
    }
    
    if (checkoutDateInput) {
        checkoutDateInput.value = selectedCheckoutDate ? formatDisplayDate(selectedCheckoutDate) : '';
    }
}

function updateHotelDateDisplay() {
    const hotelDateInput = document.getElementById('hotelDateInput');
    
    if (selectedCheckinDate && selectedCheckoutDate) {
        const checkinFormatted = formatDisplayDate(selectedCheckinDate);
        const checkoutFormatted = formatDisplayDate(selectedCheckoutDate);
        hotelDateInput.value = `${checkinFormatted} - ${checkoutFormatted}`;
    } else if (selectedCheckinDate) {
        const checkinFormatted = formatDisplayDate(selectedCheckinDate);
        hotelDateInput.value = `${checkinFormatted} - Check out?`;
    } else {
        hotelDateInput.value = '';
        hotelDateInput.placeholder = 'Check in - Check out';
    }
}

// Hotel Card Favorite Button Toggle
function initializeFavoriteButtons() {
    document.querySelectorAll('.hotel-card-fav-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            btn.classList.toggle('selected');
        });
    });
}

// Utility Functions
function closeAllDropdowns() {
    hideGuestRoomDropdown();
    hideHotelDatePicker();
}

function formatMonthYear(date) {
    const options = { month: 'long', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function formatDisplayDate(date) {
    const options = { weekday: 'short', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function isSameDate(date1, date2) {
    return date1.toDateString() === date2.toDateString();
} 