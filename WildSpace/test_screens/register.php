<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Create Account</title>
    <link rel="stylesheet" href="../assets/css/auth.css">

    <style>
        .select-wrapper {
            position: relative;
            width: 100%;
        }

        .select-wrapper select {
            width: 100%;
            padding-right: 70px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
        }

        .select-wrapper::after {
            content: "";
            position: absolute;
            right: 32px;
            top: 50%;
            width: 10px;
            height: 10px;
            border-right: 3px solid white;
            border-bottom: 3px solid white;
            transform: translateY(-65%) rotate(45deg);
            pointer-events: none;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .step-title {
            font-size: 1rem;
            color: #555;
            margin-bottom: 1rem;
        }

        .role-options {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .role-card {
            flex: 1;
            background: #000;
            color: #fff;
            padding: 1.2rem;
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            border: 2px solid #000;
            transition: 0.2s ease;
        }

        .role-card:hover {
            transform: translateY(-2px);
        }

        .role-card input {
            display: none;
        }

        .role-card.selected {
            background: #fff;
            color: #000;
            border: 2px solid #000;
        }

        .button-row {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .back-button {
            padding: 0.8rem 2rem;
            background: transparent;
            color: #000;
            border: 2px solid #000;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }

        .next-button {
            padding: 0.8rem 2rem;
            background: #000;
            color: #fff;
            border: 2px solid #000;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="nav-link">Home</a>
                <a href="#" class="nav-link">About Us</a>
            </div>
            
            <div class="logo">
                <h1 class="logo-text">WildSpace</h1>
            </div>
            
            <div class="nav-right">
                <a href="#" class="nav-link">Reservation</a>
                <button class="cta-button nav-cta">Contact Us</button>
            </div>
        </div>
    </nav>

    <!-- Main Auth Section -->
    <section class="auth-section register-section">
        <div class="auth-left">
            <div class="auth-image-container" style="width: 650px; height: 650px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                <img 
                    src="../assets/images/registerimg.png" 
                    alt="Group Image" 
                    class="auth-image"
                    style="width: 100%; height: 100%; object-fit: contain;"
                >
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-form-container">
                <h1 class="auth-title">Your <span class="auth-title-bold">productive</span> starts with us</h1>
                <p class="auth-subtitle">Create account</p>

                <form class="auth-form" id="registerForm" action="../actions/register_action.php" method="POST">

                    <!-- STEP 1 -->
                    <div class="form-step active" id="step1">
                        <p class="step-title">Step 1 of 3: Account Credentials</p>

                        <div class="form-group">
                            <input 
                                type="email"
                                name="email"
                                id="email"
                                class="form-input" 
                                placeholder="Institutional Email:" 
                                required
                            >
                        </div>

                        <div class="form-group">
                            <input 
                                type="password" 
                                name="password"
                                id="password"
                                class="form-input" 
                                placeholder="Password:" 
                                required
                            >
                        </div>

                        <div class="form-group">
                            <input 
                                type="password"
                                name="confirm_password"
                                id="confirm_password"
                                class="form-input" 
                                placeholder="Confirm Password:" 
                                required
                            >
                        </div>

                        <button type="button" class="next-button" onclick="goToStep2()">Next</button>
                    </div>

                    <!-- STEP 2 -->
                    <div class="form-step" id="step2">
                        <p class="step-title">Step 2 of 3: Select Account Type</p>

                        <div class="role-options">
                            <label class="role-card" id="studentCard">
                                <input type="radio" name="user_type" value="student">
                                Student
                            </label>

                            <label class="role-card" id="adminCard">
                                <input type="radio" name="user_type" value="admin">
                                Admin
                            </label>
                        </div>

                        <div class="button-row">
                            <button type="button" class="back-button" onclick="showStep(1)">Back</button>
                            <button type="button" class="next-button" onclick="goToStep3()">Next</button>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div class="form-step" id="step3">
                        <p class="step-title">Step 3 of 3: Personal Details</p>

                        <div class="form-group">
                            <input 
                                type="text"
                                name="firstname"
                                id="firstname"
                                class="form-input" 
                                placeholder="First Name:" 
                                required
                            >
                        </div>

                        <div class="form-group">
                            <input 
                                type="text"
                                name="lastname"
                                id="lastname"
                                class="form-input" 
                                placeholder="Last Name:" 
                                required
                            >
                        </div>

                        <div class="form-group">
                            <div class="select-wrapper">
                                <select 
                                    name="gender" 
                                    id="gender"
                                    class="form-input" 
                                    required
                                >
                                    <option value="" disabled selected>Gender:</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Prefer not to say">Prefer not to say</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <input 
                                type="text"
                                name="mobile_number"
                                id="mobile_number"
                                class="form-input" 
                                placeholder="Mobile Number:" 
                                maxlength="11"
                                required
                            >
                        </div>

                        <div class="button-row">
                            <button type="button" class="back-button" onclick="showStep(2)">Back</button>
                            <button type="submit" name="register" class="auth-submit-button">Create Account</button>
                        </div>
                    </div>
                </form>

                <div class="auth-footer">
                    <p class="auth-footer-text">Have an account? <a href="login.php" class="auth-link">Log-in</a></p>
                    
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Facebook">f</a>
                        <a href="#" class="social-icon" title="LinkedIn">in</a>
                        <a href="#" class="social-icon" title="Instagram">📷</a>
                        <a href="#" class="social-icon" title="Email">✉</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function showStep(stepNumber) {
            document.querySelectorAll('.form-step').forEach(step => {
                step.classList.remove('active');
            });

            document.getElementById('step' + stepNumber).classList.add('active');
        }

        function goToStep2() {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (email === "" || password === "" || confirmPassword === "") {
                alert("Please fill out all account credential fields.");
                return;
            }

            if (!email.endsWith("@cit.edu")) {
                alert("Only institutional emails ending with @cit.edu are allowed.");
                return;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return;
            }

            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

            if (!passwordPattern.test(password)) {
                alert("Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
                return;
            }

            showStep(2);
        }

        function goToStep3() {
            const selectedRole = document.querySelector('input[name="user_type"]:checked');

            if (!selectedRole) {
                alert("Please select whether you are a Student or Admin.");
                return;
            }

            showStep(3);
        }

        const roleCards = document.querySelectorAll('.role-card');

        roleCards.forEach(card => {
            card.addEventListener('click', function () {
                roleCards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input').checked = true;
            });
        });
    </script>
</body>
</html>