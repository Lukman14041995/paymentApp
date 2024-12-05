<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit and Withdrawal</title>

    <!-- Menambahkan beberapa styling CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-top: 20px;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #3498db;
            color: #fff;
        }
        table td {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .no-transactions {
            text-align: center;
            color: #e74c3c;
            font-size: 18px;
        }
        .message {
            text-align: center;
            font-size: 16px;
            color: #27ae60;
            margin-top: 20px;
        }
        @media screen and (max-width: 768px) {
            table th, table td {
                padding: 10px;
                font-size: 14px;
            }
        }
        .logout-btn, .payment-btn {
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
        .logout-btn:hover, .payment-btn:hover {
            background-color: #29B960FF;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Transaction History</h1>
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ url('/dashboard') }}" class="payment-btn">Back</a>
            
        </div>
        <!-- Pesan jika transaksi kosong -->
        @if ($transactions->isEmpty())
            <p class="no-transactions">No transactions found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->order_id }}</td>
                            <td>{{ number_format($transaction->amount, 2) }}</td>
                            <td>{{ ucfirst($transaction->status) }}</td>
                            <td>{{ ucfirst($transaction->type) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(session('message'))
            <p class="message">{{ session('message') }}</p>
        @endif
    </div>

</body>
</html>
