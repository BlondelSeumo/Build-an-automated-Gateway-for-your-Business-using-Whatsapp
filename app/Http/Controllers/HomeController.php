<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    

    public function index(Request $request){
        $numbers = $request->user()->devices()->latest()->paginate(15);
      
       
        $user = $request->user()->withCount(['devices','campaigns'])->
        withCount(['blasts as blasts_pending' => function($q){
            return $q->where('status', 'pending');
        }])->withCount(['blasts as blasts_success' => function($q){
            return $q->where('status', 'success');
        }])->withCount(['blasts as blasts_failed' => function($q){
            return $q->where('status', 'failed');
        }])->withCount('messageHistories')->find($request->user()->id);

       

        $user['expired_subscription_status'] = $user->expiredSubscription;
        $user['subscription_status'] = $user->isExpiredSubscription ? 'Expired' : $user->active_subscription;
        return view('home',compact('numbers','user'));
    }

    public function store(Request $request){
      
       $validate =  validator($request->all(),[
            'sender' => 'required|min:8|max:1000|unique:devices,body',
        ]);

        if($request->user()->isExpiredSubscription){
            return back()->with('alert',['type' => 'danger','msg' => 'Your subscription has expired, please renew your subscription.']);
        }
        if($validate->fails()){
            return back()->with('alert',['type' => 'danger','msg' => $validate->errors()->first()]);
        }

       if($request->user()->limit_device <= $request->user()->devices()->count() ){
            return back()->with('alert',['type' => 'danger','msg' => 'You have reached the limit of devices!']);
        }
        $request->user()->devices()->create(['body' => $request->sender,'webhook' => $request->urlwebhook]);
        return back()->with('alert',['type' => 'success','msg' => 'Devices Added!']);
    }


    public function destroy(Request $request){
        try {
            //code...
             $device = $request->user()->devices()->find($request->deviceId);
             $device->delete();
            Session::forget('selectedDevice');
            if (!empty($device->body)) {
                $path = base_path('credentials/'.$device->body);
                if(file_exists($path)){
                    File::deleteDirectory($path);
                }
            }
            return back()->with('alert',['type' => 'success','msg' => 'Devices Deleted!']);
        } catch (\Throwable $th) {
            throw $th;
            return back()->with('alert',['type' => 'danger','msg' => 'Something went wrong!']);
        }
    }


    public function setHook(Request $request){
            clearCacheNode();  
      return $request->user()->devices()->whereBody($request->number)->update(['webhook' => $request->webhook]);
    }


    public function setSelectedDeviceSession(Request $request){
        $device = $request->user()->devices()->whereId($request->device)->first();
        if(!$device){
            return response()->json(['error' => true, 'msg' => 'Device not found!']);
            Session::forget('selectedDevice');
            
        }
        session()->put('selectedDevice', [
            'device_id' => $device->id,
            'device_body' => $device->body,
        ]);
        return response()->json(['error' => false, 'msg' => 'Device selected!']);
    }


    


    

}
