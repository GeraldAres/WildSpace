document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const loginCard = document.getElementById('loginCard');

    if (loginForm && loginCard) {
        loginForm.addEventListener('submit', (e) => {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                e.preventDefault();

                loginCard.style.animation = 'shake 0.4s ease-in-out';

                setTimeout(() => {
                    loginCard.style.animation = '';
                }, 400);

                return;
            }

            const btn = loginForm.querySelector('.auth-submit-button');
            btn.innerText = 'Logging in...';
            btn.style.opacity = '0.7';
        });
    }

    const registerForm = document.getElementById('registerForm');
    const registerSubmit = document.getElementById('registerSubmit');
    const registerMessage = document.getElementById('registerMessage');

    if (registerForm && registerSubmit && registerMessage) {
        const defaultButtonText = registerSubmit.textContent;

        const showRegisterMessage = (type, text) => {
            registerMessage.textContent = text;
            registerMessage.hidden = false;
            registerMessage.classList.remove('auth-message--error', 'auth-message--success');
            registerMessage.classList.add(type === 'success' ? 'auth-message--success' : 'auth-message--error');
        };

        const hideRegisterMessage = () => {
            registerMessage.hidden = true;
            registerMessage.textContent = '';
            registerMessage.classList.remove('auth-message--error', 'auth-message--success');
        };

        const handleRegister = async () => {
            hideRegisterMessage();

            registerSubmit.disabled = true;
            registerSubmit.textContent = 'Creating account...';
            registerSubmit.style.opacity = '0.7';

            try {
                const formData = new FormData(registerForm);
                formData.append('register', '1');

                const response = await fetch(registerForm.action, {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    showRegisterMessage('success', data.message);
                    registerForm.reset();
                } else {
                    showRegisterMessage('error', data.message);
                }
            } catch {
                showRegisterMessage('error', 'Something went wrong. Please try again.');
            } finally {
                registerSubmit.disabled = false;
                registerSubmit.textContent = defaultButtonText;
                registerSubmit.style.opacity = '1';
            }
        };

        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            handleRegister();
        });

        registerSubmit.addEventListener('click', handleRegister);
    }
});

const style = document.createElement('style');
style.innerHTML = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        75% { transform: translateX(8px); }
    }
`;
document.head.appendChild(style);
