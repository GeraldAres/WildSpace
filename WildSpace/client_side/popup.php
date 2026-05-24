<?php
if (!empty($_SESSION['popup_success']) || !empty($_SESSION['popup_error'])) {

    $isSuccess = !empty($_SESSION['popup_success']);

    $message = $isSuccess
        ? $_SESSION['popup_success']
        : $_SESSION['popup_error'];
?>

<div class="popup-overlay active" id="systemPopup">

    <div class="popup-card">

        <div class="popup-icon <?php echo $isSuccess ? 'success' : 'error'; ?>">
            <i class="fas <?php echo $isSuccess ? 'fa-check' : 'fa-triangle-exclamation'; ?>"></i>
        </div>

        <h2>
            <?php echo $isSuccess ? 'Success' : 'Notice'; ?>
        </h2>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

        <button type="button"
                class="popup-close"
                onclick="closePopup()">
            OK
        </button>

    </div>

</div>

<?php
    unset($_SESSION['popup_success']);
    unset($_SESSION['popup_error']);
}
?>