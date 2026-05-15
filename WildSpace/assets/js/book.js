document.addEventListener('DOMContentLoaded', () => {
    const bookingForm = document.getElementById('bookingForm');
    const bookingDate = document.getElementById('bookingDate');
    const capacity = document.getElementById('capacity');
    const timeSlot = document.getElementById('timeSlot');
    const summary = document.getElementById('bookingSummary');

    if (!bookingForm || !bookingDate || !capacity || !timeSlot || !summary) return;

    // Prevent past dates
    const today = new Date().toISOString().split('T')[0];
    bookingDate.setAttribute('min', today);

    function updateSummary() {
        if (bookingDate.value && capacity.value && timeSlot.value) {
            summary.style.display = 'block';

            const peopleText = capacity.value == "1"
                ? "Solo Seat"
                : `Table for ${capacity.value}`;

            summary.innerHTML = `
                <strong>Booking Summary:</strong><br>
                📅 ${bookingDate.value} | 🕒 ${timeSlot.options[timeSlot.selectedIndex].text}<br>
                📍 ${peopleText}
            `;
        }
    }

    bookingDate.addEventListener('change', updateSummary);
    capacity.addEventListener('change', updateSummary);
    timeSlot.addEventListener('change', updateSummary);

    bookingForm.addEventListener('submit', () => {
        const btn = bookingForm.querySelector('.auth-submit-button');
        btn.innerText = 'Checking availability...';
        btn.style.opacity = '0.7';
    });
});