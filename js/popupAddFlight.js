document.addEventListener("DOMContentLoaded", function () {
    const addBtn = document.querySelector('.add-passenger-btn');
    const popupOverlay = document.getElementById('popup-overlay');
    const popupBody = document.getElementById('popup-body');
  
    addBtn.addEventListener('click', () => {
      fetch('pass_info_popup.php')
        .then(res => res.text())
        .then(html => {
          popupBody.innerHTML = html;
          popupOverlay.classList.remove('hidden');
          document.body.classList.add('blurred');
  
          // Add Save behavior inside the loaded popup
          setTimeout(() => {
            const saveBtn = popupBody.querySelector('.popup-save-btn');
            const closeBtn = popupBody.querySelector('.popup-close');
  
            closeBtn?.addEventListener('click', () => {
              popupOverlay.classList.add('hidden');
              document.body.classList.remove('blurred');
            });
  
            saveBtn?.addEventListener('click', function (e) {
              e.preventDefault();
  
              const fname = popupBody.querySelector('#first_name').value.trim();
              const lname = popupBody.querySelector('#last_name').value.trim();
              const gender = popupBody.querySelector('#gender').value;
              const country = popupBody.querySelector('#country').value;
  
              if (!fname || !lname) return;
  
              const passengerList = document.querySelector('.passenger-list');
              const passengerItem = document.createElement('div');
              passengerItem.classList.add('passenger-item');
              passengerItem.innerHTML = `
                <input type="checkbox" checked>
                <div class="passenger-details">
                  <span class="passenger-name">${fname} ${lname}</span>
                  <span class="passenger-type">Adult / ${gender} / ${country}</span>
                </div>
                <button><i class="fa-solid fa-user-pen"></i></button>
              `;
              passengerList.appendChild(passengerItem);
              //for child container
              passengerItem.querySelector('.edit-passenger-btn').addEventListener('click', () => {
                const name = passengerItem.querySelector('.passenger-name').textContent.trim();
                const typeText = passengerItem.querySelector('.passenger-type').textContent.trim();
                const [type, gender, country] = typeText.split(" / ");
                const [firstName, ...lastNameParts] = name.split(" ");
                const lastName = lastNameParts.join(" ");
              
                fetch('pass_info_popup.php')
                  .then(res => res.text())
                  .then(html => {
                    popupBody.innerHTML = html;
                    popupOverlay.classList.remove('hidden');
                    document.body.classList.add('blurred');
              
                    // Populate the fields
                    document.getElementById('first_name').value = firstName;
                    document.getElementById('last_name').value = lastName;
                    document.getElementById('gender').value = gender.toLowerCase();
                    document.getElementById('country').value = country.toLowerCase();
              
                    setupPopupEvents();
                  });
              });
              
              popupOverlay.classList.add('hidden');
              document.body.classList.remove('blurred');
            });
          }, 100);
        });
    });
// Handle Edit Passenger Buttons
document.querySelectorAll('.edit-passenger-btn').forEach(button => {
  button.addEventListener('click', () => {
    const name = button.parentElement.querySelector('.passenger-name').textContent.trim();
    const typeText = button.parentElement.querySelector('.passenger-type').textContent.trim();
    const [type, gender, country] = typeText.split(" / ");
    const [firstName, ...lastNameParts] = name.split(" ");
    const lastName = lastNameParts.join(" ");

    fetch('pass_info_popup.php')
      .then(res => res.text())
      .then(html => {
        popupBody.innerHTML = html;
        popupOverlay.classList.remove('hidden');
        document.body.classList.add('blurred');

        // Populate the fields
        document.getElementById('first_name').value = firstName;
        document.getElementById('last_name').value = lastName;
        document.getElementById('gender').value = gender.toLowerCase();
        document.getElementById('country').value = country.toLowerCase();

        setupPopupEvents();
      });
  });
});

function setupPopupEvents() {
  const saveBtn = popupBody.querySelector('.popup-save-btn');
  const closeBtn = popupBody.querySelector('.popup-close');

  closeBtn?.addEventListener('click', () => {
    popupOverlay.classList.add('hidden');
    document.body.classList.remove('blurred');
  });

  saveBtn?.addEventListener('click', function (e) {
    e.preventDefault();
    popupOverlay.classList.add('hidden');
    document.body.classList.remove('blurred');
  });
}


});
  