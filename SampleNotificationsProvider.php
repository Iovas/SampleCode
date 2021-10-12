<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Lead;
use App\Models\User;
use App\Models\Task;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class NotificationProvider extends ServiceProvider
{
  public function boot()
  {    

    View::composer('*', function ($view) {
      if(auth()->user()){

        $unreadNotifications = auth()->user()->unreadNotifications;

        
        foreach ($unreadNotifications as &$key) { 
          
         $lead_id = $key->data['lead_id'];  
         if($key->type=='App\Notifications\Task') { 
          $user_id = $key->notifiable_id; 
          $task_id = $key->data['task_id'];
          $key->setAttribute('task', Task::find($task_id));
        } 
        else if($key->type=='App\Notifications\Forms') { $user_id = 3; }
        
        $key->setAttribute('lead', Lead::find($lead_id));
        $key->setAttribute('user', User::find($user_id));

      }

      $readNotifications = auth()->user()->readNotifications;

      foreach ($readNotifications as &$key) { 
       $lead_id = $key->data['lead_id'];  
       if($key->type=='App\Notifications\Task') { 
        $user_id = $key->notifiable_id;
        $task_id = $key->data['task_id'];
        $key->setAttribute('task', Task::find($task_id)); 
      } 
      else if($key->type=='App\Notifications\Forms') { 
        $user_id = 4; 
      }
      
      $key->setAttribute('lead', Lead::find($lead_id));
      $key->setAttribute('user', User::find($user_id));
      
    }


    $view->with('readNotifications',  $readNotifications);
    $view->with('unreadNotifications',  $unreadNotifications);
  }
});
  }
}
