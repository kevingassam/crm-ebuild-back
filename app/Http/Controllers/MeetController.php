<?php

namespace App\Http\Controllers;

use App\Notifications\MeetCanceled;
use App\Notifications\MeetUpdated;
use Illuminate\Http\Request;
use App\Models\personnel;
use App\Models\client;
use App\Models\Meet;
use App\Mail\MeetCreated as MeetCreatedMail;
use App\Mail\MeetUpdated as MeetUpdatedMail;
use App\Mail\MeetDeleted as MeetDeletedMail;
use App\Notifications\MeetCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
class MeetController extends Controller
{
    function getclientandpersonnel(){
        $client=client::all();
        $personnel=personnel::all();
        return response()->json([
            'clients'=>$client,
            'personnel'=>$personnel
        ],200);
    }
    function addEvent(Request $request){
        $meet = Meet::create([
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'title' => $request->input('title'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'allday' => $request->input('allDay')
        ]);
    
        $guests = $request->input('selectGuest');
        foreach ($guests as $guest) {
            if ($guest['role'] === 'clients') {
                $client = Client::findorfail($guest['id']);
                $meet->client()->attach($client);
            } else {
                $personnel = Personnel::findorfail($guest['id']);
                $meet->personnel()->attach($personnel);
            }
        }
        
        $meet->save();
        foreach ($guests as $guest) {
            if ($guest['role'] === 'clients') {
                $client = Client::findorfail($guest['id']);
                $notif=new MeetCreated($meet->getKey(),$client->id,$meet->url,$meet->start,$meet->end,$meet->title,$meet->description); 
                Notification::send($client, $notif);
                Mail::to($client)->send(new MeetCreatedMail($meet));
            } else {
                $personnel = Personnel::findorfail($guest['id']);
                $notif=new MeetCreated($meet->getKey(),$personnel->id,$meet->url,$meet->start,$meet->end,$meet->title,$meet->description);
                Notification::send($personnel, $notif);
                Mail::to($personnel)->send(new MeetCreatedMail($meet));
            }
        }
        
        return response()->json([
            'message' => 'event created successfully'
        ], 200);
    }
    function getmeets(){
        $meets = Meet::with('personnel', 'client')->get();
        return response()->json($meets,200);
    }
    function updateEvent($id,Request $request){
        $meet=meet::findOrFail($id);
        $meet->url=$request->input('url');
        $meet->description=$request->input('description');
        $meet->title=$request->input('title');
        $meet->start = $request->input('start');
        $meet->end = $request->input('end');
        $meet->allday = $request->input('allDay');
        $meet->client()->detach();
        $meet->personnel()->detach();
        $guests = $request->input('selectGuest');
        foreach ($guests as $guest) {
            if ($guest['role'] === 'clients') {
                $client = Client::findorfail($guest['id']);
                $meet->client()->attach($client);
                $notif=new MeetUpdated($id,$client->id,$meet->url,$meet->start,$meet->end,$meet->title,$meet->description);
                Notification::send($client, $notif);
                Mail::to($client)->send(new MeetUpdatedMail($meet));
            } else {
                $personnel = Personnel::findorfail($guest['id']);
                $meet->personnel()->attach($personnel);
                $notif=new MeetUpdated($id,$personnel->id,$meet->url,$meet->start,$meet->end,$meet->title,$meet->description);
                Notification::send($personnel, $notif);
                Mail::to($personnel)->send(new MeetUpdatedMail($meet));
            }
        }
        $meet->save();
    }
    /**
 * Delete an event and notify its guests.
 *
 * @param  int  $id
 * @return \Illuminate\Http\JsonResponse
 */
    function deleteEvent($id)
{
    $meet = Meet::findOrFail($id);
    $personnels = $meet->personnel;
    $clients = $meet->client;

    foreach ($personnels as $personnel) {
        $notify = new MeetCanceled($meet->id, $personnel->id, $meet->title);
        Notification::send($personnel, $notify);
        Mail::to($personnel)->send(new MeetDeletedMail($meet));
        Log::info($personnel->id);
    }

    foreach ($clients as $client) {
        $notify = new MeetCanceled($meet->id, $client->id, $meet->title);
        Notification::send($client, $notify);
        Mail::to($client)->send(new MeetDeletedMail($meet));
        Log::info($client->id);
    }

    $meet->delete();

    return response()->json(['message' => 'Meet deleted successfully'], 200);
    }
}
