<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Devis;
use App\Models\Operation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\DevisPdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EbuildData;
class DevisController extends Controller
{
    public function store(Request $request){

        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'client_email' => 'required|string|email|max:255',
            'operations' => 'required|array|min:1',
            'operations.*.nature' => 'required|string|max:255',
            'operations.*.quantité' => 'required|numeric|min:0',
            'operations.*.montant_ht' => 'required|numeric|min:0',
            'operations.*.taux_tva' => 'numeric|min:0',
          //  'operations.*.discount' => 'required|string|max:255',
            'dpourcentage'=> 'nullable|numeric|min:0',
            'discountType'=> 'nullable|string|max:255',
            'TTCToggleButton' => 'boolean',
            'note' => 'nullable|string|max:255',
        ]);

        $client = Client::where('email', $request->input('client_email'))->first();
        $calculateTtc = $request->input('TTCToggleButton');

        

        $devis = Devis::create([
            'client'=>$client->name,
            'client_email' => $request['client_email'],
            'client_id' => $client->id,
            'nombre_operations' => count($request['operations']),
            'date_creation' => now(),
            'discount'=>$request['dpourcentage'],
            'note' => $request->input('note'),

        ]);


        $discountType = $request->input('discountType');
        $disp = $request->input('dpourcentage');
        $totalMontantHt =0;

        foreach ($request->input('operations') as $operationData) {
            $tauxTva = isset($operationData['taux_tva']) ? $operationData['taux_tva'] : 19; // Use 19 as default if taux_tva is not provided

            $operation = new Operation([
                'nature' => $operationData['nature'],
                'quantité' => $operationData['quantité'],
                'montant_ht' => $operationData['montant_ht'],
                'taux_tva' => $tauxTva,
            //    'discount' => $operationData['discount'],

            ]);

            if ($calculateTtc) {
               $operation->montant_ttc = $operationData['montant_ht'] * (1 + $tauxTva / 100);
           }

            $devis->operations()->save($operation);
            $totalMontantHt += $operationData['montant_ht'] * $operationData['quantité'];

        }
/*
        if ($discountType!=null && $discountType="price"){
            $totalMontantHt = $totalMontantHt-$disp ;


        }
        if ($discountType!=null && $discountType="percentage"){
            $totalMontantHt = $totalMontantHt*(1-$disp/100) ;
        }

       */
        $originalTotalMontantHt = $totalMontantHt; // Store the original total price

        if ($discountType!= null && $discountType == "price") {
            $totalMontantHt = $totalMontantHt - $disp;
        }
        if ($discountType!= null && $discountType == "percentage") {
            $totalMontantHt = $originalTotalMontantHt * (1 - $disp / 100); // Use the original total price
        }

        $devis->update([
            'total_priceht' => $totalMontantHt,
        ]);

        return response()->json($devis, 201);
    }
    public function generate($id,Request $request)
    {
        /*$user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }*/
        $devis = Devis::with('operations')->findOrFail($id);

        // Retrieve the client by email
        $client = Client::where('email', $devis->client_email)->first();
        $ebuilddata = EbuildData::first();

        // Retrieve the phone number and RNE from the client object
        $phone_number = $client->phone_number;
        $RNE = $client->RNE;

        // Convertir le montant total en toutes lettres
        $totalPriceWithTax = (float)$devis->total_priceht*1.19 + 1;
        $totalPriceWithTaxInWords = $this->convertMontantToLetters($totalPriceWithTax);

        // Passer la valeur convertie à la vue
        // Pass the image path to the view
        $imagePath = url('storage/images/' . basename($ebuilddata->logo));
        \Log::info("Image Path: " . $imagePath);  // Log the image path

        $pdf = PDF::loadView('pdf.devis', compact('devis', 'phone_number', 'RNE', 'ebuilddata', 'totalPriceWithTaxInWords', 'imagePath'));

        return $pdf->download('devis.pdf');
    }
   public function convertMontantToLetters($montant)
   {
       $units = [
           '',
           'un',
           'deux',
           'trois',
           'quatre',
           'cinq',
           'six',
           'sept',
           'huit',
           'neuf',
       ];
   
       $tens = [
           '',
           'dix',
           'vingt',
           'trente',
           'quarante',
           'cinquante',
           'soixante',
           'soixante-dix',
           'quatre-vingt',
           'quatre-vingt-dix',
       ];
   
       $hundreds = [
           '',
           'cent',
           'deux-cent',
           'trois-cent',
           'quatre-cent',
           'cinq-cent',
           'six-cent',
           'sept-cent',
           'huit-cent',
           'neuf-cent',
       ];
       $thousands = [
        '',
        'mille',
        'deux-mille',
        'trois-mille',
        'quatre-mille',
        'cinq-mille',
        'six-mille',
        'sept-mille',
        'huit-mille',
        'neuf-mille',
    ];
    
       $montant = number_format($montant, 2, '.', '');
       $intPart = (int)$montant;
       $decPart = (int)($montant * 100) % 100;
   
       $result = '';
   
       if ($intPart >= 1000) {
           $thousands = (int)($intPart / 1000);
           if (isset($units[$thousands])) {
               $result .= $units[$thousands] . ' mille ';
           }
           $intPart %= 1000;
       }
   
       if ($intPart >= 100) {
           $hundredsValue = (int)($intPart / 100);
           if (isset($hundreds[$hundredsValue])) {
               $result .= $hundreds[$hundredsValue] . ' ';
           }
           $intPart %= 100;
       }
   
       if ($intPart >= 20) {
           $tensValue = (int)($intPart / 10);
           if (isset($tens[$tensValue])) {
               $result .= $tens[$tensValue];
           }
           $intPart %= 10;
           if ($tensValue == 7 || $tensValue == 9) {
               $result = rtrim($result, 'e');
           }
           $result .= '-';
       } elseif ($intPart >= 10) {
           $specialTens = ['dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize'];
           $result .= $specialTens[$intPart - 10] . '-';
           $intPart = 0;
       }
   
       if ($intPart > 0) {
           if (isset($units[$intPart])) {
               $result .= $units[$intPart] . ' ';
           }
       }
   
       $result .= 'Dinars';
   
       if ($decPart > 0) {
           $result .= ' et ' . $decPart . ' Centimes';
       }
   
       return $result;
   }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'client_email' => 'required|string|email|max:255',
            'operations' => 'required|array|min:1',
            'operations.*.nature' => 'required|string|max:255',
            'operations.*.quantité' => 'required|numeric|min:0',
            'operations.*.montant_ht' => 'nullable|numeric|min:0',
            'operations.*.montant_ttc' => 'nullable|numeric|min:0',

            'operations.*.taux_tva' => 'numeric|min:0',
           // 'operations.*.discount' => 'required|string|max:255',
            'dpourcentage'=> 'nullable|numeric|min:0',
            'discountType'=> 'nullable|string|max:255',

            'calculate_ttc' => 'boolean',
           // 'invoiced'=>'numeric|default:0',
            'note' => 'nullable|string|max:255',
        ]);

        $client = Client::where('email', $request->input('client_email'))->first();
        $switch = $request->input('switch', false);
        $devis = Devis::findOrFail($id);
        $calculateTtc = $request->input('TTCToggleButton');

      

        // Delete existing operations
        $devis->operations()->delete();
        $discountType = $request->input('discountType');
        $disp = $request->input('dpourcentage');
        $totalMontantHt =0;

        foreach ($request->input('operations') as $operationData) {
            $tauxTva =  19; // Use 19 as default if taux_tva is not provided

            $operation = new Operation([
                'nature' => $operationData['nature'],
                'quantité' => $operationData['quantité'],
                'montant_ht' => $operationData['montant_ht'],
                'taux_tva' => $tauxTva,
            //    'discount' => $operationData['discount'],

                // 'montant_ttc' => $operationData['montant_ht'] * (1 + $operationData['taux_tva'] / 100),
                // 'montant_ttc' => $operationData['montant_ht'] * (1 + $tauxTva / 100),
                'devis_id' => $devis->id, // Assign the devis_id to the operation
            ]);
        //    $operation->montant_ttc = $operationData['montant_ht'] * (1 + $tauxTva / 100);

            if ($calculateTtc) {
                $operation->montant_ttc = $operationData['montant_ht'] * (1 + $tauxTva / 100);
            }
            $totalMontantHt += $operationData['montant_ht'] * $operationData['quantité'];
            $operation->save(); // Save the operation

            $devis->operations()->save($operation);
            $devis->operations()->save($operation); // Associate the operation with the devis
        }


        if ($discountType!=null && $discountType=="price"){
            $totalMontantHt = $totalMontantHt-$disp ;


        }
        if ($discountType!=null && $discountType=="percentage"){
            $totalMontantHt = $totalMontantHt*(1-$disp/100) ;
        }

        $updateddevis=[
            'client'=>$client->name,
            'client_email' => $request['client_email'],
            'client_id' => $client->id,
            'invoiced'=>$request['invoiced'],
            'note'=>$request['note'],
            'nombre_operations' => count($request['operations']),
            'date_creation' => now(),
            'discount'=>$request['dpourcentage'],
            'total_priceht'=> $totalMontantHt,

        ];

        $devis->update($updateddevis);


        return response()->json($devis, 200);
    }
    public function senPdf($id, Request $request)
    {
        $devis = Devis::with('operations')->findOrFail($id);
         
        // Retrieve the client by email
        $client = Client::where('email', $devis->client_email)->first();

        // Retrieve the phone number and RNE from the client object
        $phone_number = $client->phone_number;
        $RNE = $client->RNE;
        $ebuilddata = EbuildData::first();
        $totalPriceWithTax = (float)$devis->total_priceht*1.19 + 1;
        $totalPriceWithTaxInWords = $this->convertMontantToLetters($totalPriceWithTax);

        // Passer la valeur convertie à la vue
        // Pass the image path to the view
        $imagePath = url('storage/images/' . basename($ebuilddata->logo));
        \Log::info("Image Path: " . $imagePath);  // Log the image path

        $pdf = PDF::loadView('pdf.devis', compact('devis', 'phone_number', 'RNE', 'ebuilddata', 'totalPriceWithTaxInWords', 'imagePath'));
        Mail::to($devis->client_email)->send(new DevisPdf($devis, $pdf));
        //response
        return $pdf->download('devis.pdf');
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $devis = Devis::findOrFail($id);
        $devis->delete();

        return response()->json(null, 204);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')&!$user->hasRole('client')) {
            abort(403, 'Unauthorized action.');
        }
        $devis = Devis::with('operations')->findOrFail($id);

        return response()->json($devis, 200);
    }
    public function showall(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')&!$user->hasRole('client')) {
            abort(403, 'Unauthorized action.');
        }
        $devis = Devis::with('operations')->get();

        if ($user->hasRole('admin')) {
            $devis = Devis::with('operations')->get();
        } else {
            $devis = Devis::where('client_email', $user->email)->with('operations')->get();
        }

        return response()->json($devis, 200);
    }
}
