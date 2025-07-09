// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeAirportDropdowns();
    initializeDatePicker();
    initializePassengerDropdown();
    initializeCardEffects();
    initializeTestimonialAnimations();
});

document.getElementById('searchBtn')?.addEventListener('click', function () {
    const fromInput = document.getElementById('fromAirport');
    const toInput = document.getElementById('toAirport');
    const fromCode = fromInput?.getAttribute('data-code');
    const toCode = toInput?.getAttribute('data-code');
    const fromText = fromInput?.value;
    const toText = toInput?.value;

    // --- CRUCIAL CHANGE: Get dates from data-selected, which should be YYYY-MM-DD ---
    // If your date picker in script.js is properly setting data-selected attributes
    // on the internal departDate and returnDate inputs, this is the correct way.
    // Ensure updateDateFieldDisplay in script.js sets these.
    const departDateISO = document.getElementById('departDate')?.dataset.selected; // Use data-selected
    const returnDateISO = document.getElementById('returnDate')?.dataset.selected; // Use data-selected

    // If script.js's date picker does NOT set data-selected yet,
    // then you need to fix its updateDateFieldDisplay to set it,
    // OR parse the date from its display value with more robust logic here:
    // This is the fallback if data-selected isn't available, but less reliable:
    // const departDateDisplay = document.getElementById('dateInput')?.value; // Get from main dateInput for display
    // const departDateISO = parseDisplayDate(departDateDisplay); // Using the parseDisplayDate from script.js

    const adults = parseInt(document.getElementById('adultCount')?.textContent || '1', 10);
    const children = parseInt(document.getElementById('childCount')?.textContent || '0', 10);

    const trip = document.getElementById('roundTrip')?.checked ? 'round' : 'one';
    const classId = 'EC';

    // Validation needs to check the ISO date now
    if (!fromCode || !toCode || !departDateISO) {
      alert("Please complete all required fields before searching.");
      return;
    }

    if (fromCode === toCode) {
        alert("Departure and destination airports cannot be the same.");
        return;
    }

    const searchData = {
      from: fromCode,
      to: toCode,
      fromText,
      toText,
      departDate: departDateISO, // Save as ISO
      returnDate: returnDateISO || '', // Save as ISO or empty string
      adults,
      children,
      trip,
      classId
    };

    sessionStorage.setItem('flightSearch', JSON.stringify(searchData));

    // Redirect to flightBook.php without query parameters
    window.location.href = 'flightBook.php';
});
  

// Airport Dropdown Functionality
function initializeAirportDropdowns() {
    const fromInput = document.getElementById('fromAirport');
    const toInput = document.getElementById('toAirport');
    const fromDropdown = document.getElementById('fromDropdown');
    const toDropdown = document.getElementById('toDropdown');
    
    // Initialize dropdowns
    setupDropdown(fromInput, fromDropdown, 'from');
    setupDropdown(toInput, toDropdown, 'to');
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.search-input') && !event.target.closest('.date-picker-wrapper')) {
            closeAllDropdowns();
        }
    });
}

function setupDropdown(input, dropdown, type) {
    const searchInput = input.closest('.search-input');
    
    // Click on input to show dropdown
    input.addEventListener('click', function(e) {
        e.stopPropagation();
        closeAllDropdowns();
        showDropdown(dropdown, searchInput);
    });
    
    // Handle airport option selection
    dropdown.addEventListener('click', function(e) {
        const option = e.target.closest('.airport-option');
        if (option) {
            selectAirport(option, input, dropdown, searchInput, type);
        }
    });
}

function showDropdown(dropdown, searchInput) {
    dropdown.classList.add('show');
    searchInput.classList.add('active');
}

function hideDropdown(dropdown, searchInput) {
    dropdown.classList.remove('show');
    searchInput.classList.remove('active');
}

function closeAllDropdowns() {
    const dropdowns = document.querySelectorAll('.airport-dropdown, .date-picker-dropdown, .passenger-dropdown');
    const searchInputs = document.querySelectorAll('.search-input, .date-picker-wrapper');
    
    dropdowns.forEach(dropdown => dropdown.classList.remove('show'));
    searchInputs.forEach(input => input.classList.remove('active'));
}

function selectAirport(option, input, dropdown, searchInput, type) {
    const city = option.dataset.city;
    const code = option.dataset.code;
    const name = option.dataset.name;
    
    // Update input value
    input.value = `${city} (${code})`;
    input.setAttribute('data-code', code);
    input.setAttribute('data-city', city);
    input.setAttribute('data-name', name);
    
    // Update visual selection
    const allOptions = dropdown.querySelectorAll('.airport-option');
    allOptions.forEach(opt => opt.classList.remove('selected'));
    option.classList.add('selected');
    
    // Hide dropdown
    hideDropdown(dropdown, searchInput);
    
    // Remove the selected airport from the other dropdown options
    updateOtherDropdown(type, code);
}

function updateOtherDropdown(selectedType, selectedCode) {
    const otherDropdown = selectedType === 'from' ? 
        document.getElementById('toDropdown') : 
        document.getElementById('fromDropdown');
    
    const allOptions = otherDropdown.querySelectorAll('.airport-option');
    
    allOptions.forEach(option => {
        if (option.dataset.code === selectedCode) {
            option.style.display = 'none';
        } else {
            option.style.display = 'block';
        }
    });
}

// Comprehensive Date Picker System
let currentDate = new Date();
let selectedDepartDate = null;
let selectedReturnDate = null;
let isSelectingReturn = false;

function initializeDatePicker() {
    const dateInput = document.getElementById('dateInput');
    const datePickerDropdown = document.getElementById('datePickerDropdown');
    const datePickerWrapper = document.querySelector('.date-picker-wrapper');
    
    // Trip type radio buttons
    const roundTripRadio = document.getElementById('roundTrip');
    const oneWayRadio = document.getElementById('oneWay');
    
    // Date field inputs
    const departDateInput = document.getElementById('departDate');
    const returnDateInput = document.getElementById('returnDate');
    const returnField = document.getElementById('returnField');
    
    // Navigation buttons
    const prevMonthBtn = document.querySelector('.prev-month');
    const nextMonthBtn = document.querySelector('.next-month');
    const doneBtn = document.querySelector('.btn-done');
    
    // Initialize calendar
    generateCalendar();
    
    // Date input click handler
    dateInput.addEventListener('click', function(e) {
        e.stopPropagation();
        closeAllDropdowns();
        showDatePicker();
    });
    
    // Trip type change handlers
    roundTripRadio.addEventListener('change', function() {
        if (this.checked) {
            returnField.style.display = 'flex';
            updateDateDisplay();
        }
    });
    
    oneWayRadio.addEventListener('change', function() {
        if (this.checked) {
            returnField.style.display = 'none';
            selectedReturnDate = null;
            isSelectingReturn = false;
            departDateInput.parentElement.classList.add('focused');
            returnDateInput.parentElement.classList.remove('focused');
            updateDateDisplay();
            generateCalendar();
        }
    });
    
    // Date field click handlers
    departDateInput.addEventListener('click', function(e) {
        e.stopPropagation();
        isSelectingReturn = false;
        departDateInput.parentElement.classList.add('focused');
        returnDateInput.parentElement.classList.remove('focused');
        generateCalendar();
    });
    
    returnDateInput.addEventListener('click', function(e) {
        e.stopPropagation();
        if (roundTripRadio.checked) {
            isSelectingReturn = true;
            returnDateInput.parentElement.classList.add('focused');
            departDateInput.parentElement.classList.remove('focused');
            generateCalendar();
        }
    });
    
    // Navigation handlers
    prevMonthBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar();
    });
    
    nextMonthBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar();
    });
    
    // Done button handler
    doneBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (selectedDepartDate) {
            updateDateDisplay();
            hideDatePicker();
        }
    });
    
    // Stop propagation for dropdown content
    datePickerDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
}

function showDatePicker() {
    const datePickerDropdown = document.getElementById('datePickerDropdown');
    const datePickerWrapper = document.querySelector('.date-picker-wrapper');
    
    datePickerDropdown.classList.add('show');
    datePickerWrapper.classList.add('active');
    generateCalendar();
}

function hideDatePicker() {
    const datePickerDropdown = document.getElementById('datePickerDropdown');
    const datePickerWrapper = document.querySelector('.date-picker-wrapper');
    
    datePickerDropdown.classList.remove('show');
    datePickerWrapper.classList.remove('active');
}

function generateCalendar() {
    const month1 = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const month2 = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
    
    // Update month headers
    // These lines replace "January 2025" and "February 2025"
    document.getElementById('currentMonth1').textContent = formatMonthYear(month1);
    document.getElementById('currentMonth2').textContent = formatMonthYear(month2);
    
    // Generate calendars
    generateMonthCalendar(month1, 'calendarDates1');
    generateMonthCalendar(month2, 'calendarDates2');
}

function generateMonthCalendar(date, containerId) {
    const container = document.getElementById(containerId);
    const year = date.getFullYear();
    const month = date.getMonth();
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();
    
    // Clear container
    container.innerHTML = '';
    
    let dayCount = 1;
    const today = new Date();
    
    // Generate calendar grid (6 weeks)
    for (let week = 0; week < 6; week++) {
        const weekRow = document.createElement('div');
        weekRow.className = 'calendar-row';
        
        for (let day = 0; day < 7; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-date';
            
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
                dayElement.dataset.date = formatDateISO(currentDay);
                
                // Add classes based on date state
                if (currentDay >= today) {
                    dayElement.classList.add('available');
                    dayElement.addEventListener('click', () => selectDate(currentDay));
                } else {
                    dayElement.classList.add('past');
                }
                
                // Highlight selected dates
                if (selectedDepartDate && isSameDate(currentDay, selectedDepartDate)) {
                    dayElement.classList.add('selected');
                }
                
                if (selectedReturnDate && isSameDate(currentDay, selectedReturnDate)) {
                    dayElement.classList.add('selected');
                }
                
                // Highlight range
                if (selectedDepartDate && selectedReturnDate && 
                    currentDay > selectedDepartDate && currentDay < selectedReturnDate) {
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

function selectDate(date) {
    const roundTripRadio = document.getElementById('roundTrip');
    
    if (!isSelectingReturn || !roundTripRadio.checked) {
        // Selecting departure date
        selectedDepartDate = new Date(date);
        isSelectingReturn = roundTripRadio.checked;
        
        if (roundTripRadio.checked) {
            // Switch to return date selection
            document.getElementById('departDate').parentElement.classList.remove('focused');
            document.getElementById('returnDate').parentElement.classList.add('focused');
            
            // Clear return date if it's before new departure date
            if (selectedReturnDate && selectedReturnDate <= selectedDepartDate) {
                selectedReturnDate = null;
            }
        }
    } else {
        // Selecting return date
        if (date > selectedDepartDate) {
            selectedReturnDate = new Date(date);
            isSelectingReturn = false;
            
            // Switch back to departure date focus
            document.getElementById('returnDate').parentElement.classList.remove('focused');
            document.getElementById('departDate').parentElement.classList.add('focused');
        }
    }
    
    updateDateFieldDisplay();
    generateCalendar();
}

function updateDateFieldDisplay() {
    const departDateInput = document.getElementById('departDate');
    const returnDateInput = document.getElementById('returnDate');

    if (selectedDepartDate) {
        departDateInput.value = formatDisplayDate(selectedDepartDate);
        departDateInput.dataset.selected = formatDateISO(selectedDepartDate); // <-- THIS LINE IS KEY
    } else {
        departDateInput.value = '';
        departDateInput.dataset.selected = ''; // <-- Clear also
    }

    if (selectedReturnDate) {
        returnDateInput.value = formatDisplayDate(selectedReturnDate);
        returnDateInput.dataset.selected = formatDateISO(selectedReturnDate); // <-- THIS LINE IS KEY
    } else {
        returnDateInput.value = '';
        returnDateInput.dataset.selected = ''; // <-- Clear also
    }
}

function updateDateDisplay() {
    const dateInput = document.getElementById('dateInput');
    const roundTripRadio = document.getElementById('roundTrip');
    
    if (selectedDepartDate) {
        const departFormatted = formatDisplayDate(selectedDepartDate);
        
        if (!roundTripRadio.checked) {
            dateInput.value = `${departFormatted} (One-way)`;
        } else if (selectedReturnDate) {
            const returnFormatted = formatDisplayDate(selectedReturnDate);
            dateInput.value = `${departFormatted} - ${returnFormatted}`;
        } else {
            dateInput.value = `${departFormatted} - Return?`;
        }
    } else {
        dateInput.value = '';
        dateInput.placeholder = 'Depart';
    }
}

// Utility Functions
function formatMonthYear(date) {
    const options = { month: 'long', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function formatDisplayDate(date) {
    const options = { weekday: 'short', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function formatDateISO(date) {
    return date.toISOString().split('T')[0];
}

function isSameDate(date1, date2) {
    return date1.toDateString() === date2.toDateString();
}

// Passenger Dropdown Functionality
let adultCount = 1;
let childCount = 0;

function initializePassengerDropdown() {
    const passengerInput = document.getElementById('passengerInput');
    const passengerDropdown = document.getElementById('passengerDropdown');
    const passengerSection = document.getElementById('passengerSection');
    
    // Passenger input click handler
    passengerInput.addEventListener('click', function(e) {
        e.stopPropagation();
        closeAllDropdowns();
        showPassengerDropdown();
    });
    
    // Initialize counter buttons
    initializeCounterButtons();
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#passengerSection')) {
            hidePassengerDropdown();
        }
    });
    
    // Initial display update
    updatePassengerDisplay();
}

function showPassengerDropdown() {
    const passengerDropdown = document.getElementById('passengerDropdown');
    const passengerSection = document.getElementById('passengerSection');
    
    passengerDropdown.classList.add('show');
    passengerSection.classList.add('active');
}

function hidePassengerDropdown() {
    const passengerDropdown = document.getElementById('passengerDropdown');
    const passengerSection = document.getElementById('passengerSection');
    
    passengerDropdown.classList.remove('show');
    passengerSection.classList.remove('active');
}

function initializeCounterButtons() {
    const counterButtons = document.querySelectorAll('.counter-btn');
    
    counterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const type = this.dataset.type;
            const isPlus = this.classList.contains('plus');
            const isMinus = this.classList.contains('minus');
            
            if (type === 'adult') {
                if (isPlus && adultCount < 9) {
                    adultCount++;
                } else if (isMinus && adultCount > 1) {
                    adultCount--;
                }
                updateCounterDisplay('adult', adultCount);
            } else if (type === 'child') {
                if (isPlus && childCount < 9) {
                    childCount++;
                } else if (isMinus && childCount > 0) {
                    childCount--;
                }
                updateCounterDisplay('child', childCount);
            }
            
            updatePassengerDisplay();
            updateCounterButtonStates();
        });
    });
    
    // Initial button states
    updateCounterButtonStates();
}

function updateCounterDisplay(type, count) {
    const countElement = document.getElementById(type + 'Count');
    countElement.textContent = count;
}

function updateCounterButtonStates() {
    // Adult buttons
    const adultMinusBtn = document.querySelector('.counter-btn.minus[data-type="adult"]');
    const adultPlusBtn = document.querySelector('.counter-btn.plus[data-type="adult"]');
    
    adultMinusBtn.classList.toggle('disabled', adultCount <= 1);
    adultPlusBtn.classList.toggle('disabled', adultCount >= 9);
    
    // Child buttons
    const childMinusBtn = document.querySelector('.counter-btn.minus[data-type="child"]');
    const childPlusBtn = document.querySelector('.counter-btn.plus[data-type="child"]');
    
    childMinusBtn.classList.toggle('disabled', childCount <= 0);
    childPlusBtn.classList.toggle('disabled', childCount >= 9);
}

function updatePassengerDisplay() {
    const passengerInput = document.getElementById('passengerInput');
    
    let displayText = '';
    
    if (adultCount > 0) {
        displayText += `${adultCount} adult${adultCount > 1 ? 's' : ''}`;
    }
    
    if (childCount > 0) {
        if (displayText) displayText += ', ';
        displayText += `${childCount} child${childCount > 1 ? 'ren' : ''}`;
    }
    
    passengerInput.value = displayText;
    passengerInput.placeholder = displayText || '1 adult';
}

// Card Effects
function initializeCardEffects() {
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Testimonial Animations
function initializeTestimonialAnimations() {
    const testimonialSection = document.querySelector('.testimonials');
    const testimonialHeader = document.querySelector('.testimonial-header');
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    
    // Check if elements exist to prevent errors
    if (!testimonialSection || !testimonialHeader || testimonialCards.length === 0) {
        return;
    }
    
    // Function to check if an element is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8
        );
    }
    
    // Function to check if element is out of viewport (for removing animation)
    function isOutOfViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top > (window.innerHeight || document.documentElement.clientHeight)
        );
    }
    
    // Function to animate testimonials when they come into view
    function animateOnScroll() {
        // Add animation when scrolling down to the section
        if (isInViewport(testimonialSection)) {
            testimonialHeader.classList.add('animate');
            
            testimonialCards.forEach(card => {
                card.classList.add('animate');
            });
        } 
        // Remove animation when scrolling back up
        else if (isOutOfViewport(testimonialSection)) {
            testimonialHeader.classList.remove('animate');
            
            testimonialCards.forEach(card => {
                card.classList.remove('animate');
            });
        }
    }
    
    // Initial check in case the section is already in view
    animateOnScroll();
    
    // Add scroll event listener
    window.addEventListener('scroll', animateOnScroll);
} 