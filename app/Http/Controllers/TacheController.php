<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Mail\ProjectCreated;
use App\Mail\TacheCreated;
use App\Notifications\TaskCreated;
use App\Notifications\TaskCompleted;
use App\Models\Comment;
use App\Models\FilesTache;
use App\Models\personnel;
use App\Models\Project;
use App\Models\Tache;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class TacheController extends Controller
{

    public function storetache(Request $request)
    {

        $request->validate([
            'intitule' => 'required|string',
            'deadline' => 'required|date',
            'description' => 'required|string',
            'files.*' => 'nullable|file|max:2048',  
            'commentaire' => 'nullable|string',
            'projectname' => 'required|string|exists:projects,projectname',
        ]);
        $user = $request->user();

        // Check if user is the admin
        
        $project = Project::where('projectname', $request->input('projectname'))->firstOrFail();

        $tache = new Tache();
        $tache->intitule = $request->input('intitule');
        $tache->deadline = $request->input('deadline');
        $tache->description = $request->input('description');
        $tache->projectname = $project->projectname; // set the projectname
        $tache->project_id = $project->id; // set the project_id
        $tache->save();
        if ($request->hasFile('file')) {

            $files = $request->file('file');
          foreach ($files as $file) {

              if ($file) {
            //  return response()->json([ $file], 201);
              $media = new FilesTache();
              $media->file_name = $file->getClientOriginalName();
              $media->file_path = $file->store('public/files');
              $tache->files()->save($media);
          }
          }
        }
        // Assign staff to the tache
        $staff = $project->personnel()->pluck('Name')->toArray(); // get the staff of the project
        $staffInput = json_decode($request->input('staff'));
        foreach ($staff as $staffName) {
            if (in_array($staffName, $staffInput)) {
                $personnel = Personnel::where('Name', $staffName)->firstOrFail();
                $tache->personnel()->attach($personnel); 
                $notif=new TaskCreated($project->id,$personnel->id,$project->projectname,$tache->description,$tache->intitule,$tache->getKey()); 
                Notification::send($personnel, $notif);
            }
        }

        // Send email to all staff
        $staffEmails = Personnel::whereIn('Name', $staff)->pluck('email')->toArray();
        Mail::to($staffEmails)->send(new TacheCreated($tache));
        
        // Save the comment, if provided
        $comment = $request->input('commentaire');
        if ($comment) {
            $tache->comments()->create(['comment' => $comment]);
        }
        
        
        return response()->json(['message' => 'Tache created successfully'], 201);
    }



    public function createcomment(Tache $tache, Request $request) {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $comment = new Comment();
        $comment->comment = $request->input('comment');
        $comment->tache_id = $tache->id;
        $comment->save();

        return response()->json(['message' => 'Comment created successfully'], 201);
    }
    public function update(Request $request,$id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'intitule' => 'required|string',
                'deadline' => 'required|date',
                'description' => 'required|string',
                'files.*' => 'nullable|file|max:4096',
                'commentaire' => 'nullable|string',
                'projectname' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                $errors = $validator->errors();
                \Log::error('Error updating tache: '. $errors);
    
                return response()->json(['error' => 'Validation errors', 'errors' => $errors], 422);
            }
        $project = Project::where('projectname', $request->input('projectname'))->first();
        $tache=Tache::findOrFail($id);
        $tache->intitule = $request->input('intitule');
        $tache->deadline = $request->input('deadline');
        $tache->description = $request->input('description');
        $tache->projectname = $project->projectname;
        $tache->project_id = $project->id;

        if ($request->hasFile('file')) {
            $uploadedFiles = $request->file('file');
            foreach ($uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    $media = new FilesTache();
                    $media->file_name = $uploadedFile->getClientOriginalName();
                    $media->file_path = $uploadedFile->store('files');
                    $tache->files()->save($media);
                }
            }
        }

        $tache->save();

        // Update staff assigned to the tache
        $staff = json_decode($request->input('staff'), true);
        $tache->personnel()->detach(); // remove existing staff
        if ($request->has('staff')) {
            $staff = json_decode($request->input('staff'), true);
            $tache->personnel()->detach(); // remove existing staff
            foreach ($staff as $staffName) {
                $personnel = Personnel::where('Name', $staffName)->firstOrFail();
                $tache->personnel()->attach($personnel);
            }
        }

        // Update comment, if provided
        $comment = $request->input('commentaire');
        if ($comment) {
            $tache->comments()->delete(); // remove existing comment
            $tache->comments()->create(['comment' => $comment]);
        }

        return response()->json(['message' => 'Tache updated successfully'], 200);
    } catch (\Exception $e) {
        \Log::error('Error updating tache: ' . $e->getMessage());

        return response()->json(['error' => 'An error occurred while updating the tache '.$id], 500);
    }
    }
    public function destroy(Tache $tache)
    {
        $tache->delete();

        return response()->json(['message' => 'Tache deleted successfully'], 200);
    }
   /* public function show(Tache $tache)
    {
        $tache->load('comments');

        return response()->json(['tache' => $tache], 200);
    }*/
    public function showtachespersonnel(Request $request,$email){
        
        $personnel=Personnel::where('email',$email)->firstOrFail();
        $taches=$personnel->taches()->get();
        return response()->json($taches,200);
    }
    public function showtachesimportantpersonnel($email){
        $personnel=Personnel::where('email',$email)->firstOrFail();
        $taches=$personnel->taches()->where('important',true)->with('project')->get();
        
        return response()->json($taches,200);
    }
    public function showtachescompletedpersonnel($email){
        $personnel=Personnel::where('email',$email)->firstOrFail();
        $taches=$personnel->taches()->where('status','Completed')->with('project')->get();
        return response()->json($taches,200);
    }
    public function showtachesaffectedpersonnel($email){
        $personnel=Personnel::where('email',$email)->firstOrFail();
        $taches=$personnel->taches()->where('status','Affected')->with('project')->get();
        return response()->json($taches,200);
    }
    public function showtachesproject($id){
        $taches=Tache::where('project_id',$id)->with('project')->get();

        return response()->json($taches, 200);
    }
    public function showtachescompletedproject($id){
        $taches=Tache::where('project_id',$id)->where('status','Completed')->with('project')->get();

        return response()->json($taches, 200);
    }
    public function showtachesaffectedproject($id){
        $taches=Tache::where('project_id',$id)->where('status','Affected')->with('project')->get();

        return response()->json($taches, 200);
    }
    public function showtachesfavorisproject($id){
        $taches=Tache::where('project_id',$id)->where('important',true)->with('project')->get();
        return response()->json($taches, 200);
    }
    public function show($id)
    {
           $tache = Tache::with('personnel')->findOrFail($id);

           // Rename the personnel attribute to staff
           $tache->setAttribute('staff', $tache->getAttribute('personnel'));
           $tache->unsetRelation('personnel');
           $fileUrls = $tache->files->pluck('url');
           // Remove the pivot object from each staff member
           foreach ($tache->staff as $staffMember) {
               $staffMember->makeHidden('pivot');
           }

           return response()->json(['tache' => $tache, 'file_urls' => $fileUrls]);
    }
  public function showall(Request $request)
    {
        $taches = tache::all();
        return response()->json($taches, 200);
    }
    function changeCompleted($id)
{
    $tache = Tache::find($id);
    if ($tache->status == "InProgress") {
        $tache->status = "Completed";
    } else {
        $tache->status = "InProgress";
    }
    $users = User::where('role', 'admin')->get();
    $personnels = $tache->personnel->pluck('name'); // or pluck('id') or whatever column you want to log
    log::info($personnels);
    $notif = new TaskCompleted($tache->project_id, $personnels, $tache->projectname, $tache->intitule);
    Notification::send($users, $notif);
    $tache->save();
    return response()->json(['Success' => 'Status changed'], 200);
}
    function addFavori($id)
    {
     $tache = Tache::find($id);
            $tache->important=!($tache->important);
            $tache->save();

            return response()->json(['Success' => 'Status changed'], 200);
    }
    public function getFavori()
    {

        $tasksFavori = Tache::where('important', true)->get();

        return response()->json($tasksFavori, 200);
    }

    public function getCompleted()
    {
        $completedTasks = Tache::where('status', 'Completed')->get();

        return response()->json($completedTasks, 200);
    }
    public function getAffected()
    {
        $completedTasks = Tache::where('status', 'Affected')->get();

        return response()->json($completedTasks, 200);
    }
   public function commentsByTache(Request $request, $id)
   {
       $comments = Comment::where('tache_id', $id)->get();

       return response()->json($comments);
   }

   public function changeAccept($id){
    $tache=Tache::findOrFail($id);
    $tache->status="InProgress";
    $tache->save();
    return response()->json(['Success' => 'Status changed'], 200);
   }





     public function getTacheStatusStatistics()
                    {
                        $statistics = Tache::groupBy('status')
                            ->select('status', \DB::raw('count(*) as count'))
                            ->get()
                            ->pluck('count', 'status')
                            ->toArray();

                        return $statistics;
                    }






}
