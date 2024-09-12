<?php

namespace App\Http\Controllers;

use Dompdf\Options;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;
use App\Models\EbuildData;
use Illuminate\Support\Facades\Log;


class EbuildDataController extends Controller
{

    public function getData()
    {
        $data = EbuildData::first(); // Fetch the first record, adjust as necessary
        if(!$data){
            return response()->json(
                [
                    'error' => 'No data found',
                    'code' => 404,
                    'statut' => false
                ]
            );
        }
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'mf' => 'required|string',
            'address' => 'required|string',
            'rib' => 'required|string',
            // adjust the mime types and max size as needed
        ]);

        $id = 1;
        $ebuildData = EbuildData::findOrFail($id);
        log::info("hello");
        log::info($request['name']);
        log::info($request);
        log::info($request['logo']);
        $ebuildData->name = $request['name'];
        $ebuildData->mail = $request['email'];
        $ebuildData->address = $request['address'];
        $ebuildData->phone_number = $request['phone'];
        $ebuildData->matriculef = $request['mf'];
        $ebuildData->rib = $request['rib'];

        if ($request->hasFile('logo')) {
            $filename = $request->file('logo');
            $ebuildData->logo = $filename->store('public/images');
        }

        $ebuildData->save();

        try {
            // validation and update logic here
            return response()->json(['message' => 'Ebuild data updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating ebuild data: ' . $e->getMessage()], 422);
        }
    }
}
