<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $devis->formatted_id }}</title>
    <style>
        /* Add any custom CSS styles for the PDF here */
        body {
            font-family: Tahoma, sans-serif;
            font-size: 12px;
        }
        .facture-header {
            padding: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        border-top: 2px solid black;
        text-align: center;
        padding: 10px 0;
    }
        .header img {
            width: 100px;
            height: 100px;
        }
        .header h1 {
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;

            margin-top: 20px;
        }
        table th, table td {
            border-bottom: 1px solid black;

            padding: 5px;
        }
        table th {
            background-color: #fceeef;
            font-weight: bold;
        }

        .totals {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .totals p {
            margin: 0;
            margin-left: 20px;
            font-size: 14px;
        }
        .vertical-text {
            position: absolute;
            left: 0;
            top: 50%;
            transform: rotate(-90deg);
            transform-origin: 0 0;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: -30px;
            height: 100%;
            color: #fa012e;
            font-size: 23px;
            width: 1000px;
            font-family: Bold, Tahoma /* Adjust the width to fit your text */
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .client-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .clear {
            clear: both;
        }

    </style>
</head>
<body>
<div  style="margin-top:24%" class="vertical-text"><strong style="color: #000000;">DEVIS </strong>N°{{ $devis->formatted_id }}</div>
<div class="header">
<div style="display: flex; align-items: center;">
<div style="flex: 1;">
    <div class="facture-header">
        <div class="company-info">
            <div class="col-8">
        <h2 style="margin-bottom: 0px; font-size: 30px; font-family: Helvetica, sans-serif; color: #fa012e;">{{ strtoupper($ebuilddata->name) }}</h2>
        <p><strong>MF: {{$ebuilddata->name}}, SARL immatriculée au registre national </strong></p>
        <p><strong>des entreprises sous l’identiant unique {{$ebuilddata->matriculef}}.</strong></p>
        <p><strong>{{$ebuilddata->phone_number}}</strong></p>
        <p>{{$ebuilddata->mail}}</p>
        <p>{{$ebuilddata->address}}</p>
        </div>
        <div class="col-4">
        <div style="margin-left: 20px;"> <img src="" alt="" style="width: 100px; height: 100px;"></div></div>
            <div style="margin-right: 140px; margin-top:27px">
                <p style="font-size: 20px; font-family: Bold, Helvetica, sans-serif; display: inline-block;color: #fa012e;">De</p>
                <hr style="border: 1px solid #fa012e;">
                <p style="font-size: 16px; background-color: #edeaea;">{{ strtoupper($ebuilddata->name) }}</p>
                </div>
        </div>
        <div class="client-info"> 
            <div style="margin-top:248px; margin-right: 230px; text-align: left; margin-left: -100px;">
                <p style="font-size: 20px; font-family: Bold, Helvetica, sans-serif; display: inline-block;color: #fa012e;">À</p>
                <hr style="border: 1px solid #fa012e;">
                <p style="font-size: 16px; background-color: #edeaea;">{{ $devis->client }}</p>
                <p><strong>Email:</strong> {{ $devis->client_email }}</p>
                <p><strong>N° de téléphone:</strong> {{ $phone_number }}</p>
                <div style="margin-top: -150px;">
            <h1 style="margin-right: -198px; text-align: right;"><strong>Numéro </strong><small>{{ $devis->formatted_id }}</small></h1>
            <h1 style="margin-right: -198px; text-align: right;"><strong>Date </strong><small>{{ $devis->created_at->format('d/m/Y ') }}</small></h1>
        </div>
            </div>
        </div> 
        
        <div class="clear"></div>
    </div> </div>
    
    </div>

</div>

<table style="margin-left: 20px; ">
    <thead>
    <tr>
        <th>NATURE DE L'OPERATION</th>
        <th><strong>QUANTITÉ </strong></th>
        <th>MONTANT HT</th>
        <th>TAXES</th>
        @if (!is_null($devis->total_priceht))
        <th>Montant TTC</th>
        <th>TOTAL HT</th>

        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($devis->operations as $operation)
        <tr>
            <td>{{ $operation->nature }}</td>
            <td>x {{ $operation->quantité }}</td>
            <td>{{ number_format((float)$operation->montant_ht, 2) }}</td>
            <td>{{ $operation->taux_tva }}</td>
            @if (!is_null($devis->total_priceht))
            <td>{{ number_format((float)$operation->montant_ttc, 2) }}</td>
            <td>{{number_format((float) $devis->total_priceht) }}</td>
            @endif
        </tr>
    @endforeach
    @if (!is_null($devis->note))
        <tr>
        <td colspan="{{!is_null($devis->total_priceht)? 5 : 3}}" >
            <strong>Note:</strong> {{ $devis->note }}</td>
    </tr>
    @endif
    </tbody>
</table>

<div class="totals" style="margin-top:20px">
<table style="width: 200px; float: left; margin-left: 20px; text-align: right;">
  <thead>
    <tr>
      <th><strong>*</strong></th>
      <th><strong>BASE</strong></th>
      <th><strong>TAUX</strong></th>
      <th><strong>TAXE</strong></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>19</td>
      <td>{{ number_format((float)$devis->total_priceht) }}</td>
      <td>19%</td>
      <td>{{ number_format((float)$devis->total_priceht*0.19 -1)}}</td>
    </tr>
    <tr>
      
      <td>TIM</td>
      <td>0,000</td>
      <td></td>
      <td>1,000</td>
    </tr>
    <tr>
      <th><strong>TOTAL</strong></th>
      <th></th>
      <th></th>
      <th><strong>{{ number_format((float)$devis->total_priceht*0.19) }}</strong></th>
    </tr>
  </tbody>
</table>
    <div style="width: 250px; float: left; margin-left: 20px;">
        <h3><strong>Arrêter La Présente Facture A La Somme De:</strong></h3>
        <h3>{{ $totalPriceWithTaxInWords }}</h3>
        
    </div>
    <table style="width: 200px; float: left; margin-left: 20px; text-align: right;">
        <tr>
            <th><strong>Total Montant HT</strong></th>
            <td>{{ number_format((float)$devis->total_priceht) }}<strong>DT</strong></td>
        </tr>
        @if (!is_null($devis->operations->first()->montant_ttc))
            <tr>
                <th><strong>TAXES</strong></th>
                <td>{{ number_format((float)$devis->total_priceht*0.19) }}</td>
            </tr>
        @endif
        
        <tr>
            <th><strong>TOTAL À PAYER</strong></th>
            <td>{{ number_format((float)$devis->total_priceht*1.19+1) }}<strong>DT</strong></td>
        </tr>
    </table>
    <div style="clear: both;"></div>
</div>
<div class="footer">
</div>
</body>
</html>