<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Task;
use App\Models\Note;
use App\Models\User;
use App\Models\LeadSource;
use Carbon\Carbon;

class passportAuthController extends Controller

{

    public function loginUser(Request $request){

        $login_credentials=[
            'email'=>$request->email,
            'password'=>$request->password,
        ];

        if(auth()->attempt($login_credentials)){
            // generam tokenul pentru user
            $user_login_token= auth()->user()->createToken('Trading.md_CRM')->accessToken;
            //intoarcem tokenul ca json
            return response()->json(['token' => $user_login_token], 200);
        }
        else{
            // email/pass incorect, error code 401
            return response()->json(['error' => 'UnAuthorised Access'], 401);
        }
    }

    /**
     * Detaliile utilizatorului logat (dupa ce intoarcem tokenul)
     */
    public function authenticatedUserDetails(){
        return response()->json(['authenticated-user' => auth()->user()], 200);
    }

    /**
     * API Trading.Club -> CRM. (club/includes/registerLead.php)
     */
    public function registerClubLead(Request $request){

       try{
        //Gasim Sursa Lead-ului in CRM
         $source = LeadSource::where('source','Club')->first();

        //Lead Nou
         $lead = new Lead();
         $lead->firstName = $request->fname;
         $lead->lastName = $request->lname;
         $lead->clubUsername = $request->username;
         $lead->email = $request->email;
         $lead->clubEmail = $request->email;
         $lead->phone = $request->phone;

         /**
         * Pe viitor cand va fi posibilitatea la mai mult de un telefon
            $lead->clubPhone = $request->phone;
            **/
        $lead->source_id = $source->id; //required
        
        // Hard Coded SYSTEM ID - Lead-ul trebuie aprobat si assigned la Sales dupa ce vine in CRM
        $lead->user_id = 7; 
        
        // vedem daca daca lead-ul nu e dublicat dupa email
        $dupeEmail = Lead::where('email','LIKE',$request->email)->get();
        if($dupeEmail->count()>0) {  $lead->duplicateId = 1; }

        // vedem daca daca lead-ul nu e dublicat dupa telefon
        $dupePhone = Lead::where('phone','LIKE',$request->phone)->get();
        if($dupePhone->count()>0) {  $lead->duplicateId = 1; }
        $lead->save();
        
        
        // Task pentru lead-ul nou
        $task = new Task;
        $task->lead_id=$lead->id;
        $task->due = Carbon::now();
        $task->title = "S-a inregistrat pe TradingClub!";
        $task->user_id = $lead->user_id;
        $task->save();

        // Comentariu descriptiv de unde vine Lead-ul (Note si Notification)
        $comment= "S-a inregistrat pe Club. ( User:". $lead->ClubUsername." )";

        // Salvam si in activitatile facute de lead  
        $note = new Note; 
        $note->lead_id=$lead->id;
        $note->note = $comment;
        $note->save();

        // Trimitem Notificare in CRM administratorului sa aprobe lead-ul  
        $details = [
            'title' => $comment,
            'date' => Carbon::now(),
            'lead_id' => $lead->id
        ];

        $user = User::findOrFail(1);
        $user->notify(new \App\Notifications\Forms($details));
    }

    catch(\Exception $e){
       // Eroare
     $response =  $e->getMessage();  
 }
}
}
