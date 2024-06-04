<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewClientMail;
use App\Models\Client;

class ClientController extends Controller
{
    public function storeclient(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

       $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'social_reason' => 'required|string|max:255',
            'RNE' => 'nullable|file|mimes:pdf|max:3072',
            'confirmation' => 'nullable|string',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.string' => 'L\'adresse e-mail doit être une chaîne de caractères.',
            'email.email' => 'L\'adresse e-mail doit être une adresse e-mail valide.',
            'email.max' => 'L\'adresse e-mail ne peut pas dépasser 255 caractères.',
            'email.unique' => 'L\'adresse e-mail est déjà utilisée.',

            'phone_number.required' => 'Le numéro de téléphone est obligatoire.',
            'phone_number.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'phone_number.max' => 'Le numéro de téléphone ne peut pas dépasser 255 caractères.',

            'address.required' => 'L\'adresse est obligatoire.',
            'address.string' => 'L\'adresse doit être une chaîne de caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',

            'social_reason.required' => 'La raison sociale est obligatoire.',
            'social_reason.string' => 'La raison sociale doit être une chaîne de caractères.',
            'social_reason.max' => 'La raison sociale ne peut pas dépasser 255 caractères.',

            'RNE.file' => 'Le fichier RNE doit être un fichier.',
            'RNE.mimes' => 'Le fichier RNE doit être un fichier PDF.',
            'RNE.max' => 'Le fichier RNE ne peut pas dépasser 3 Mo.',

        ]);
        
        

        $client = new Client();
        $client->name = $request->input('name');
        $client->email = $request->input('email');
        $client->phone_number = $request->input('phone_number');
        $client->address = $request->input('address');
        $client->social_reason = $request->input('social_reason');
        if ($request->hasFile('RNE')) {
            //upload in storage link public folder
            $name = $request->file('RNE')->store('uploads/posts', 'public');
            $client->RNE = $name;
        }
        // Generate a random 10 char password from below chars
        $random = str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890/+-*');
        $password = substr($random, 0, 10);
        $client->confirmation = $request->input('confirmation') ? true :  false;
        $client->password = $password;

        // Create a new user with role client in the user table
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($password);
        $user->role = 'client';
        if ($user->save()) {
            if ($request->input('confirmation') ? true :  false) {
                Mail::to($client->email)->send(new NewClientMail($client, $password));
                $client->save();
            }
        }



        return response()->json(
            [
                'success' => true,
                'message' => 'Client créé avec succès',
            ]
        );
    }











    public function updatec(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:clients,email,' . $id,
            'phone_number' => 'string|max:255',
            'address' => 'string|max:255',
            'social_reason' => 'string|max:255',
            'RNE' => 'string|max:255',
            'confirmation' => 'nullable|boolean',
        ]);

        $client = Client::find($id);
        $userEmail = $client->email;
        $clientConfirmation = $client->confirmation;
        if ($client) {
            $client->name = $request->input('name') ?? $client->name;
            $client->email = $request->input('email') ?? $client->email;
            $client->phone_number = $data['phone_number'] ?? $client->phone_number;
            $client->address = $data['address'] ?? $client->address;
            $client->social_reason = $data['social_reason'] ?? $client->social_reason;
            $client->RNE = $data['RNE'] ?? $client->RNE;
            $client->confirmation = $request->input('confirmation'); // Update the confirmation attribute
            if (($data['confirmation'] <> $clientConfirmation && $data['confirmation']) || ($data['confirmation'] && $userEmail <> $client->email)) {
                $password = $client->password;
                Mail::to($client->email)->send(new NewClientMail($client, $password));
            }
            $client->save();
            // Find the user record for the personnel
            $user = User::where('email', $userEmail)->first();
            if ($user) {
                // Update the user record for the personnel
                $user->name =  $client->name;
                $user->email = $client->email;
                $user->save();
            } else {
                return response()->json(['error' => 'User not found'], 404);
            }

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Client not found'], 404);
        }
    }
    public function deletec(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $client = Client::find($id);

        if ($client) {
            $client->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Client not found'], 404);
        }
    }

    public function viewallc(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $clients = Client::all();
        return response()->json(
            [
                'clients' => $clients
            ]
        );
    }
}
