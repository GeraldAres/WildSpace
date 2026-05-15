document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const responseMsg = document.getElementById('responseMessage');

    contactForm.addEventListener('submit', function(e) {
        // Prevent default only for demo purposes/visual feedback
        // If you want actual PHP submission, remove e.preventDefault()
        e.preventDefault();

        // Change button state
        submitBtn.disabled = true;
        submitBtn.innerText = 'Sending...';

        // Simulate a network delay
        setTimeout(() => {
            // Show Success Message
            responseMsg.innerText = 'Message sent! We will get back to you soon.';
            responseMsg.className = 'status-msg success-txt';
            responseMsg.style.display = 'block';

            // Reset form
            contactForm.reset();
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerText = 'Send Message';

            // Hide message after 5 seconds
            setTimeout(() => {
                responseMsg.style.display = 'none';
            }, 5000);
        }, 1500);
    });
});