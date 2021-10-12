<?php

namespace App\Imports;
use Carbon\Carbon;
use App\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportLeads implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
   public function model(array $row)
    {




$fname = $row[24];
if(empty($row[2])&&!empty($row[24])) {$fname = $row[24];} else {$fname=$row[2];}
$dupe = Null;
$ld = Lead::where('Email',$row[5])
     ->where('Phone',$row[6])
     ->where('ClubUsername',$row[23])->first();
  //   dd(Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8])));
echo "<br>";
if($row[17]&&trim($row[17]!='')) {echo Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[17]));}
 if($ld) {
    $dupe = $ld->id;
}
if($row[17]&&trim($row[17]!='')) {$firstVisit = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[17]));} else $firstVisit= Null;
if($row[20]) {$numberOfChats = $row[20];} else $numberOfChats = 0;
if($row[21]) {$daysVisited = $row[21];} else $daysVisited = 0;
if($row[13]&&trim($row[13]!='')) {$lastActivityTime = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[13]));} else $lastActivityTime= Null;
if($row[8]&&trim($row[8]!='')) {$created = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8]));} else $created = Carbon::now();
if($row[9]&&trim($row[9]!='')) {$updated_at = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[9]));} else $updated_at = Carbon::now();
        return new Lead([

'zohoId' => $row[0],
'zohoCompany' => $row[1],
'FirstName'=> $fname,
'LastName'=> $row[3],
'Info' => $row[4],
'Email' => $row[5],
'Phone' => $row[6],
'zohoLeadSource' => $row[7],
'created_at' => $created,
'updated_at' => $updated_at,
'City' => $row[10],
'country' => $row[11],
'skype' => $row[12],
'lastActivityTime' =>  $lastActivityTime,
'regDate' => $row[14],
'brokerId' => $row[15],
'Birthday' => $row[16],
'firstVisit' => $firstVisit,
'referrer' => $row[18],
'firstPageVisited' => $row[19],
'numberOfChats' => $numberOfChats,
'daysVisited' => $daysVisited,
'Score' => $row[22],
'ClubUsername' => $row[23],
'ClubPhone' => $row[25],
'CreatedByID' => 1,
'user_id' => 1,
'approved' => 1,
'duplicateId' => $dupe,
        ]);
    }
}
