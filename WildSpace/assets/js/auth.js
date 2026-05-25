document.addEventListener("DOMContentLoaded", () => {
    const roleSelector = document.querySelector(".role-selector");
    const roleHint = document.getElementById("roleHint");

    const roleLabels = {
        student: {
            hint: "Selected: Student — book study spaces",
            hintClass: "is-student",
        },
        admin: {
            hint: "Selected: Admin — Manage Reservations",
            hintClass: "is-admin",
        },
    };

    function updateRoleHint(role) {
        if (!roleHint || !roleLabels[role]) {
            return;
        }

        const { hint, hintClass } = roleLabels[role];
        roleHint.setAttribute("data-selected-role", role);

        /* Keeps #roleHint accessible text in sync for screen readers */
        roleHint.setAttribute("aria-label", hint);

        roleHint.classList.remove("is-student", "is-admin");
        roleHint.classList.add(hintClass);
    }

    function syncRoleHintFromDom() {
        if (!roleSelector) {
            return;
        }

        const checked = roleSelector.querySelector(
            'input[type="radio"][name="role"]:checked'
        );

        if (checked) {
            updateRoleHint(checked.value);
        }
    }

    if (roleSelector) {
        const roleRadios = roleSelector.querySelectorAll(
            'input[type="radio"][name="role"]'
        );

        roleRadios.forEach((radio) => {
            radio.addEventListener("change", () => {
                if (radio.checked) {
                    updateRoleHint(radio.value);
                }
            });

            radio.addEventListener("click", () => {
                if (radio.checked) {
                    updateRoleHint(radio.value);
                }
            });
        });

        roleSelector.addEventListener("click", () => {
            queueMicrotask(syncRoleHintFromDom);
        });

        syncRoleHintFromDom();
    }

    const registerForm = document.getElementById("registerForm");
    const registerMessage = document.getElementById("registerMessage");
    const registerSubmit = document.getElementById("registerSubmit");

    if (!registerForm) {
        return;
    }

    const showMessage = (type, text) => {
        registerMessage.hidden = false;
        registerMessage.textContent = text;
        registerMessage.classList.remove(
            "auth-message--error",
            "auth-message--success"
        );
        registerMessage.classList.add(
            type === "success"
                ? "auth-message--success"
                : "auth-message--error"
        );
    };

    registerForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        registerSubmit.disabled = true;
        registerSubmit.textContent = "Creating account...";

        try {
            const formData = new FormData(registerForm);
            formData.append("register", "1");

            const response = await fetch(registerForm.action, {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

if (data.success) {
    showMessage("success", data.message);

    setTimeout(() => {
        window.location.href = data.redirect;
    }, 1000);
}else {
                showMessage("error", data.message);
            }
        } catch {
            showMessage(
                "error",
                "Something went wrong. Please try again."
            );
        }

        registerSubmit.disabled = false;
        registerSubmit.textContent = "Create Account";
    });
});

function closePopup() {
    const popup = document.getElementById("systemPopup");

    if (popup) {
        popup.classList.remove("active");
        popup.style.display = "none";
    }
}
