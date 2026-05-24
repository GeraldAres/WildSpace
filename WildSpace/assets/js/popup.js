function closePopup() {
    const popup = document.getElementById("systemPopup");

    if (popup) {
        popup.classList.remove("active");

        setTimeout(() => {
            popup.remove();
        }, 200);
    }
}