<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .form-card {
            width: 48%;
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-card h2 {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
        }

        .logout-btn,
        .payment-btn {
            background-color: #29B960FF;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            text-align: center;
            display: inline-block;
            text-decoration: none;
        }

        .logout-btn:hover,
        .payment-btn:hover {
            background-color: #29B960FF;
        }
    </style>
</head>

<body>



    <div class="container">
        <h1>Deposit and Withdrawal</h1>
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ url('/dashboard') }}" class="payment-btn">Back</a>
        </div>
        <div class="form-container">
            <!-- Form Deposit -->
            <div class="form-card">
                <h2>Deposit</h2>
                <form method="POST" action="{{ url('/deposit') }}">
                    @csrf
                    <input type="text" name="order_id" value="{{ $depoId }}" required readonly>
                    <input type="number" name="amount" placeholder="Amount" required step="0.01">
                    <button type="submit">Deposit</button>
                </form>
            </div>

            <!-- Form Withdrawal -->
            <div class="form-card">
                <h2>Withdraw</h2>
                <form action="{{ route('withdrawal') }}" method="POST">
                    @csrf
                    <input type="text" name="order_id" value="{{ $widthId }}" required readonly>
                    <input type="number" step="0.01" name="amount" placeholder="Amount" required>
                    <button type="submit">Withdraw</button>
                </form>
            </div>
        </div>

        <!-- Menampilkan Pesan -->
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Menambahkan SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('message'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('message') }}',
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
</body>

</html>
