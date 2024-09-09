<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\MeetController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AnswersController;
use App\Http\Controllers\EbuildDataController;
use App\Http\Controllers\HelleController;
use App\Http\Controllers\TacheController;
use App\Models\Facture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::put('/user/password', 'App\Http\Controllers\AuthController@updatePassword')->middleware('auth');
Route::view('reset-password/{token}', 'auth.reset-password')->name('password.reset');
Route::post('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'forgot']);
Route::post('reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset']);

Route::post('/hello', [HelleController::class, 'hello']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);



Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('changePassword', [AuthController::class, 'ChangePassword']);
    Route::get('GetInfo', [AuthController::class, 'GetInfo']);

    ////////////////////////personnel////////////////////////

    Route::post('/personnel', [AuthController::class, 'store1']);
    Route::get('/personnel',  [AuthController::class, 'index']);
    Route::delete('/personnel/{id}',  [AuthController::class, 'destroy']);
    Route::put('/personnel/{id}',  [AuthController::class, 'updatel']);
    ////////////////////////client////////////////////////

    Route::post('/clients', [ClientController::class, 'storeclient']);
    Route::put('/clients/{id}', [ClientController::class, 'updatec']);
    Route::delete('/clients/{id}', [ClientController::class, 'deletec']);
    Route::get('/clients', [ClientController::class, 'viewallc']);
    ////////////////////////facture////////////////////////

    Route::post('factures/add', [FactureController::class, 'store']);
    Route::post('/factures/send', [FactureController::class, 'sendPdfToClient']);
    Route::get('/sendpdf/{facture}', function(Facture $facture) {
        $controller = new FactureController(); // Replace with your actual controller name
        $controller->sendPdfCopyToClient($facture);

        return redirect()->back()->with('success', 'PDF copy sent to client successfully!');
    })->name('sendPdfCopyToClient');

    Route::put('/facture/{id}', [FactureController::class, 'update']);
    Route::delete('/facture/{id}', [FactureController::class, 'destroy']);
    Route::get('/facture/{id}', [FactureController::class, 'show']);
    Route::get('/factures', [FactureController::class, 'showall']);
    Route::get('/factures/{id}/pdf', [FactureController::class, 'generatePdf']);
    Route::get('/facture/{id}/send', [FactureController::class, 'senPdf']);

    //Route::get('/factures/{facture}/pdf', [FactureController::class, 'sendPdfToClient']);
    ////////////////////////devis////////////////////////

    Route::apiResource('devis', DevisController::class);
    Route::post('devis', [DevisController::class, 'store']);
    Route::get('/devis', [DevisController::class, 'showall']);
    Route::put('/devis/{id}', [DevisController::class, 'update']);
    Route::delete('/devis/{id}', [DevisController::class, 'destroy']);
    Route::get('/devis/{id}', [DevisController::class, 'show']);
    Route::get('devis/{id}/pdf', [DevisController::class, 'generate']);
    Route::get('/devis/{id}/send', [DevisController::class, 'senPdf']);
     ////////////////////////project////////////////////////

    Route::post('project/add', [ProjectController::class, 'store']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::get('/projects', [ProjectController::class, 'showAll']);

// Route for showing a ticket
    Route::post('/tickets', [ProjectController::class, 'storeTicket'])->name('tickets.store');
    Route::delete('/ticket/{id}', [ProjectController::class, 'deleteTicketAndAnswers']);
    Route::get('/tickets/{id}', [ProjectController::class, 'showTicket'])->name('tickets.show');
    Route::get('/ticket/client', [ProjectController::class, 'showClientTickets']);
    Route::get('/ticket/personnel', [ProjectController::class, 'viewAssignedTickets']);
    Route::get('/alltickets', [ProjectController::class, 'getAllTickets']);
    Route::put('/ticket/{id}', [ProjectController::class, 'updateTicket']);
    Route::get('/statistics/status', [ProjectController::class, 'getStatusStatistics']);
    Route::get('/statistics/priority', [ProjectController::class, 'getPriorityStatistics']);
    Route::get('/statistics/etat', [ProjectController::class, 'getEtatStatistics']);
    Route::put('/ticket/validated/{id}',[ProjectController::class,'ticketCompleted']);
    Route::put('/tickets/changeread',[ProjectController::class,'ticketChangeRead']);




// Route for answering a ticket
    Route::post('/tickets/{id}/answer', [ProjectController::class, 'answerTicket']);




// Route for get answering by ticket
    Route::get('/tickets/{id}/answers', [ProjectController::class, 'answersByTicket'])->name('tickets.answer');
     Route::delete('/answers/{id}', [AnswersController::class, 'destroy']);
     Route::put('/answers/{id}', [AnswersController::class, 'update']);
});

////////////////////////devis////////////////////////

Route::post('/tachesss', [TacheController::class, 'storetache']);
Route::post('/taches/update/{id}',[TacheController::class, 'update']);
Route::post('/taches/update/status/{id}',[TacheController::class, 'updateStatus']);
Route::delete('/taches/{tache}',[TacheController::class, 'destroy']);
Route::get('/taches/{tache}',[TacheController::class, 'show']);
Route::get('/taches/personnel/{email}',[TacheController::class, 'showtachespersonnel']);
Route::get('/taches/personnel/completed/{email}',[TacheController::class, 'showtachescompletedpersonnel']);
Route::get('/taches/personnel/affected/{email}',[TacheController::class, 'showtachesaffectedpersonnel']);
Route::get('/taches/personnel/important/{email}',[TacheController::class, 'showtachesimportantpersonnel']);
Route::get('/taches/id/{idproject}',[TacheController::class, 'showtachesproject']);
Route::get('/taches/completed/project/{idproject}',[TacheController::class, 'showtachescompletedproject']);
Route::get('/taches/favoris/project/{idproject}',[TacheController::class, 'showtachesfavorisproject']);
Route::get('/taches/affected/project/{idproject}',[TacheController::class, 'showtachesaffectedproject']);
Route::get('/taches',[TacheController::class, 'showall']);
Route::post('/taches/{tache}/comments', [TacheController::class, 'createcomment']);
Route::get('/comments/taches/{tache}', [TacheController::class, 'commentsByTache']);
Route::post('/tache/favori/{tache}',[TacheController::class, 'addFavori']);
Route::post('/tache/completed/{tache}',[TacheController::class, 'changeCompleted']);
Route::put('/tache/accept/{id}',[TacheController::class, 'changeAccept']);
Route::get('/favori', [TacheController::class, 'getFavori']);
Route::get('/completed', [TacheController::class, 'getCompleted']);
Route::get('/affected', [TacheController::class, 'getAffected']);
Route::get('/statistics/statusTache', [TacheController::class, 'getTacheStatusStatistics']);

//Route::get('/devis/{id}', [DevisController::class, 'show']);
//Route::get('/devis', [DevisController::class, 'showall']);
Route::get('devis/{id}/pdf', [DevisController::class, 'generate']);
////////////////////////notification//////////////////////////////////////////
Route::get('/notifications/all', [NotificationController::class,'showAllNotif']);
Route::get('/notifications/{personnel}', [NotificationController::class,'showNotifPersonnel']);
Route::get('/notifications/client/{client}',[NotificationController::class,'showNotifClient']);
Route::delete('/notifications/{id}',[NotificationController::class,'destroy']);
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::post('/dataebuild/{id}', [EbuildDataController::class, 'update']);
Route::get('/dataebuild', [EbuildDataController::class, 'getData']);
//////////////////////////meet////////////
Route::get('/calendarglobal',[MeetController::class,'getclientandpersonnel']);
Route::get('/calendarglobal/all',[MeetController::class,'getmeets']);
Route::post('/calendarglobal/add',[MeetController::class,'addEvent']);
Route::put('/calendarglobal/{id}',[MeetController::class,'updateEvent']);
Route::delete('/calendarglobal/{id}',[MeetController::class,'deleteEvent']);