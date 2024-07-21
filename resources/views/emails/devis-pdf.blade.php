<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DEVIS</title>
    <style>
        /* Define your CSS styles here */
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 0 auto;
            max-width: 600px;
        }

        .logo {
            max-width: 200px;
            height: auto;
        }

        .facture-header {
            background-color: #eee;
            padding: 20px;
        }

        .facture-header h1 {
            margin: 0;
        }

        .facture-header p {
            margin: 0;
        }

        .facture-body {
            padding: 20px;
        }

        .facture-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .facture-table th {
            background-color: #eee;
            text-align: left;
            padding: 10px;
        }

        .facture-table td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 10px;
        }

        .facture-total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }

        .facture-total p {
            margin: 0;
        }

        .facture-signature {
            margin-top: 50px;
            text-align: right;
        }

        .facture-signature p {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="facture-header">
        
        <h1>Devis n° {{ $devis->id }}</h1>
        <p>Date : {{ $devis->created_at->format('d/m/Y') }}</p>
        <p>Client : {{ $devis->client }}</p>
        <p>Email : {{ $devis->client_email }}</p>
    </div>

    <div class="facture-body">
        <table class="facture-table">
            <thead>
            <tr>
                <th>Nature</th>
                <th>Quantité</th>
                <th>Montant HT</th>
                <th>Taux TVA</th>
                <th>Montant HT</th>
                <th>Montant TTC</th>
            </tr>
            </thead>
            <tbody>
            @foreach($devis->operations as $operation)
                <tr>
                    <td>{{ $operation->nature }}</td>
                    <td>{{ $operation->quantité }}</td>
                    <td>{{ $operation->montant_ht }} TND</td>
                    <td>{{ $operation->taux_tva }}%</td>
                    <td>{{ $operation->montant_ht }} TND</td>
                    <td>{{ $operation->montant_ttc ?? '-' }} TND</td>
                    </tr>
            @endforeach
            </tbody>
        </table>

        <div class="facture-total">
            <p>Total HT : {{ $devis->total_priceht }} TND</p>
            <p>Total TTC : {{ number_format((float)$devis->total_priceht*1.19, 2) }} TND</p>
        </div>


    </div>
</div>
</body>
</html>