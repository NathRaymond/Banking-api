<?php

namespace App\Http\Controllers\Api;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function fetchNotification(){
        $notification = Notification::orderBy("id","DESC")->get();
        return API_Response(200, [
           "message"=>$notification
        ]);
    }
}
