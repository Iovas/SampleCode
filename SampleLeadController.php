<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;
use Config;
//use http\Env\Response;

use App\Models\Lead;
use App\Models\Note;
use App\Models\Task;
use App\Models\User;
use App\Models\LeadSource;
use App\Controllers\SingleEmailToLeads;


use Webklex\IMAP\Facades\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
//require 'vendor/autoload.php';


class LeadController extends Controller
{


    public function index()
    { 
    //if(Auth::user()->roles()->pluck('name')->contains('superadmin')) {
        $data = Lead::sortable()->where('duplicateId','=',Null)->orderBy('id', 'desc')->paginate(20);
        $dupes = Lead::sortable()->where('duplicateId','!=',Null)->orderBy('id', 'desc')->paginate(20);
        $users = User::all();
        return view('leads.index',compact('data','users','dupes'));
    //     }
    //    else return redirect('/');     
    }


    public function create()
    {  $sources = LeadSource::all();
        return view('leads.create',compact('sources'));
    }

    public function store(Request $request)
    {  
       $request->validate([
            //'FirstName' => 'required',
            //'LastName' => 'required',

       ]);
       
       Lead::create($request->all());
       
       return redirect()->route('leads')
       ->with('success','Lead created successfully.');
   }



   public function show(Lead $lead)
   {   
       $users = User::all();

// Connecting to get the sent/received emails from Lead inbox
       $url = Config::set('imap.accounts.default.username',auth()->user()->EmailUsername);
       $url = Config::set('imap.accounts.default.password',auth()->user()->EmailPassword);
       $oClient = Client::account('default');

//Pause this for now
//$oClient->connect(); 

       $aFolder = $oClient->getFolders();

       $oFolder = $oClient->getFolder('INBOX');
       $aMessage = $oFolder

       ->query()
       ->to($lead->email)
       ->from(auth()->user()->email)
       ->get(); 
//dd($aMessage->getSubject()); 
       $calls = collect($lead->call)->sortByDesc('created_at')->paginate(10);
       $emails = collect($lead->email)->sortByDesc('created_at')->paginate(10);

      return view('leads.show',compact('lead','calls','emails','aMessage','users')); //
  }

  public function edit(Lead $lead)
  {
    $sources = LeadSource::all();
    return view('leads.edit',compact('lead','sources'));
}

public function update(Request $request, Lead $lead)
{
    $lead->update($request->all());
    return redirect()->to('leads/'.$lead->id);
}

public function destroy(Lead $lead)
{    
    DB::table('notifications')->where('data->lead_id', $lead->id)->delete();
        //dd($lead->id);
    $lead->delete();
    
    return redirect()->route('leads.index')
    ->with('success','Lead deleted successfully');
}

///TEMPORARY FUNCTION
public function leadsource() {

    $leads = Lead::all();

    foreach ($leads as $lead) {
       $source = $lead->zohoLeadSource;
       $new_source = LeadSource::where('source',$source)->first();
       if (empty($new_source)) { 
        $sursa_noua = new LeadSource();
        $sursa_noua->source = $source;
        $sursa_noua->save();
        $lead->source_id = $sursa_noua->id;
    }
    else {$lead->source_id = $new_source->id;}
    $lead->update();
}

return view('leads.sources',compact('leads'));
}
////////////

public function findDupes() {

    $users = Lead::whereNotNull('email')->get();
    $usersUnique = $users->unique(['email']);
    $userDuplicates = $users->diff($usersUnique);

    foreach ($userDuplicates as $dupe) {
        $dupeEmail = Lead::where('email',$dupe->email)->get();
        foreach ($dupeEmail as $value) {
         if($value->id!=$dupe->id) {$dupe['dupe']=$value->id;}
     }
 }
 $data = $userDuplicates->paginate(10);
 return view('leads.findDupes',compact('data'));
}

public function assignUser(Request $request){
    try {
        $lead = Lead::find($request->lead_id);
        $lead->user_id = $request->user_id;
        $lead->save();
        $response = 'Succes';

    }
    catch(\Exception $e){
     $response =  $e->getMessage();   
 }

 return $response;
}


public function notduplicate(Request $request){
    try {
        $lead = Lead::find($request->lead_id);
        $lead->duplicateID = Null;
        $lead->save();
        $response = 'Succes';

    }
    catch(\Exception $e){
     $response =  $e->getMessage();   
 }
 return $response;
}


}
