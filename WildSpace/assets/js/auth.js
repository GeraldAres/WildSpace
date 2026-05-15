document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const loginCard = document.getElementById('loginCard');

    loginForm.addEventListener('submit', (e) => {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        // Basic validation for visual feedback
        if (!email || !password) {
            e.preventDefault();
            
            // Add a shake effect to the container
            loginCard.style.animation = 'shake 0.4s ease-in-out';
            
            setTimeout(() => {
                loginCard.style.animation = '';
            }, 400);
            
            return;
        }

        // Optional: Change button text to show loading
        const btn = loginForm.querySelector('.auth-submit-button');
        btn.innerText = 'Logging in...';
        btn.style.opacity = '0.7';
    });
});

// Adding shake animation via JS-injected CSS
const style = document.createElement('style');
style.innerHTML = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        75% { transform: translateX(8px); }
    }
`;
document.head.appendChild(style);