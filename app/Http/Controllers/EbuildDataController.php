<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\EbuildData;


class EbuildDataController extends Controller
{

    public function getData()
    {
        $data = EbuildData::first();
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
        ]);

        $ebuildData = EbuildData::first();
        $ebuildData->name = $request->input('name');
        $ebuildData->mail = $request->input('email');
        $ebuildData->address = $request->input('address');
        $ebuildData->phone_number = $request->input('phone');
        $ebuildData->matriculef = $request->input('mf');
        $ebuildData->rib = $request->input('rib');

        if ($request->hasFile('logo')) {
            $filename = $request->file('logo');
            $ebuildData->logo = $filename->store('public/images');
        }

        try {
             $ebuildData->save();
            // validation and update logic here
            return response()->json(['message' => 'Ebuild data updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating ebuild data: ' . $e->getMessage()], 422);
        }
    }
}
