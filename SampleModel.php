<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Lead extends Model
{

	use Sortable;
	use HasFactory;

	protected $fillable = [
		'firstName', 'lastName', 'user_id', 'phone', 'email', 'LeadSourceID', 'createdByID', 'lastLogin', 'countryID', 'city', 'address', 'clubID', 'clubUsername', 'clubEmail', 'clubPhone', 'info', 'company', 'birthday', 'languageID', 'score', 'lastLogin','created_at','client_cat','updated_at', 'zohoId','zohoCompany','zohoLeadSource', 'country', 'skype' ,'lastActivityTime','regDate','firstVisit','referrer','firstPageVisited','numberOfChats','daysVisited','approved','duplicateId','brokerId','source_id'
	];

	public $sortable = ['id','FirstName', 'LastName', 'Email', 'created_at', 'updated_at','client_cat'];

	public function leadTasks() {
		return $this->hasMany(Task::class); 
	} 
	public function call() {
		return $this->hasMany(Call::class); 
	} 
	
	public function email() {
		return $this->hasMany(SingleEmailToLead::class); 
	} 

	public function note() {
		return $this->hasMany(Note::class); 
	} 

	public function user() {
		return $this->belongsTo(User::class); 
	} 

	public function source() {
		return $this->belongsTo(LeadSource::class); 
	} 
  
	public function fullname() {
		return $this->LastName . " " . $this->FirstName;
	}
}

