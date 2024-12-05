<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .transaksi-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .transaksi-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 48%;
        }
        .transaksi-card h2 {
            color: #333;
            margin-bottom: 15px;
        }
        .transaksi-table {
            width: 100%;
            border-collapse: collapse;
        }
        .transaksi-table th, .transaksi-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .transaksi-table th {
            background-color: #f2f2f2;
            color: #333;
        }
        .transaksi-table tr:hover {
            background-color: #f9f9f9;
        }
        .logout-btn, .payment-btn {
            background-color: #3498db;
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
        .logout-btn:hover, .payment-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Payment App</h1>
        
        <!-- Logout Form -->
        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>

        <!-- Payment Button -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ url('/payment') }}" class="payment-btn">Go to Payment</a>
            <a href="{{ url('/transactions/history') }}" class="payment-btn">History</a>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <h3>Balance: ${{ number_format($wallet->balance, 2, '.', ',') }}</h3>
        </div>
        <div class="transaksi-section">
            <!-- Deposit Section -->
            <div class="transaksi-card">
                <h2>Deposit Transactions</h2>
                <table class="transaksi-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deposits as $deposit)
                            <tr>
                                <td>{{ $deposit->order_id }}</td>
                                <td>${{ number_format($deposit->amount, 2) }}</td>
                                <td>{{ $deposit->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Withdrawal Section -->
            <div class="transaksi-card">
                <h2>Withdrawal Transactions</h2>
                <table class="transaksi-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($withdrawals as $withdrawal)
                            <tr>
                                <td>{{ $withdrawal->order_id }}</td>
                                <td>${{ number_format($withdrawal->amount, 2) }}</td>
                                <td>{{ $withdrawal->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<!-- Pesan sukses -->
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
