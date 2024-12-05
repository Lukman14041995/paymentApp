<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register - Payment App</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container for form */
        .form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .form-container h1 {
            color: #2C3E50;
            margin-bottom: 30px;
            font-size: 24px;
        }

        /* Tab navigation styles */
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .tabs button {
            padding: 10px 20px;
            border: none;
            background-color: #3498db;
            color: white;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            border-radius: 5px;
            margin: 0 5px;
        }

        .tabs button.active {
            background-color: #2980b9;
        }

        /* Input field styles */
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: #3498db;
            outline: none;
        }

        /* Button styles */
        button {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #2980b9;
        }

        /* Error message styles */
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        /* Footer style */
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }

        .footer a {
            color: #3498db;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <div class="form-container">
        <h1>Payment App</h1>

        <!-- Tab navigation -->
        <div class="tabs">
            <button id="login-tab" class="active" onclick="showLoginForm()">Login</button>
            <button id="register-tab" onclick="showRegisterForm()">Register</button>
        </div>

        <!-- Login Form -->
        <div id="login-form">
            <form method="POST" action="/login">
                @csrf
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <button type="submit">Login</button>

                @if ($errors->any())
                    <div class="error-message">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>

            <div class="footer">
                <p>Don't have an account? <a href="javascript:void(0)" onclick="showRegisterForm()">Register here</a></p>
            </div>
        </div>

        <!-- Register Form -->
        <div id="register-form" style="display: none;">
            <form method="POST" action="{{ url('/register') }}">
                @csrf
                <input type="text" name="name" placeholder="Enter your name" value="{{ old('name') }}" required>
                <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <input type="password" name="password_confirmation" placeholder="Confirm your password" required>
                <button type="submit">Register</button>
            
                @if ($errors->any())
                    <div class="error-message">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
            

            <div class="footer">
                <p>Already have an account? <a href="javascript:void(0)" onclick="showLoginForm()">Login here</a></p>
            </div>
        </div>
    </div>

    <script>
        function showLoginForm() {
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-tab').classList.add('active');
            document.getElementById('register-tab').classList.remove('active');
        }

        function showRegisterForm() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
            document.getElementById('register-tab').classList.add('active');
            document.getElementById('login-tab').classList.remove('active');
        }
    </script>

</body>
</html>
