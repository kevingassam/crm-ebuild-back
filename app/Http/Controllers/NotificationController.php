<?php

namespace App\Http\Controllers;

use App\Models\notification;
use App\Models\personnel;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    
   /** 
    * @param  \Illuminate\Http\Request  $request */
    public function showAllNotif(Request $request)
{
    $notifications = Notification::where('notifiable_id', 1)->orderBy('created_at', 'desc')->get();

    return response()->json(['messages' => $notifications], 201);
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $notif = notification::where('updated_at', $data['updated_at'])->where('notifiable_id',$id)->first();
        $notif->delete();
        return response()->json(['message' => 'notification deleted'], 200);
    }
    public function showNotifTask($id,Request $request)
    {
            $personnel=personnel::where('email',$id)->firstOrFail();
            $notifications =notification::where('type','App\Notifications\TaskCreated')->where('notifiable_id',$personnel->id)
                                                                                        ->orderBy('created_at', 'desc')->get();
            return response()->json(['messages' => $notifications], 201);  
    }
}
