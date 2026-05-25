document.addEventListener("DOMContentLoaded", () => {
    const deleteForm = document.getElementById("deleteAccountForm");

    if (deleteForm) {
        deleteForm.addEventListener("submit", (e) => {
            const confirmed = confirm("Are you sure you want to delete your account? This action cannot be undone.");

            if (!confirmed) {
                e.preventDefault();
            }
        });
    }
});

function closePopup() {
    const popup = document.getElementById("systemPopup");
    if (popup) {
        popup.classList.remove("active");
        popup.style.display = "none";
    }
}

function openDeletePopup() {
    document.getElementById("deleteConfirmPopup").classList.add("active");
}

function closeDeletePopup() {
    document.getElementById("deleteConfirmPopup").classList.remove("active");
}

function submitDeleteAccount() {
    document.getElementById("deleteAccountForm").submit();
}