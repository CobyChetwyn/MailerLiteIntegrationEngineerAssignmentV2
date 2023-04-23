<?php

namespace App\Http\Controllers\subscribers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Config;
use App\Http\Controllers\api\MLAPI;
use DataTables;

class Table extends Controller
{
  /**
   * Get data for table.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
      $apiKey = Config::select('value')->where('type', 'apiKey')->first();

      if (!is_null($apiKey->value)){
          if(\request()->ajax()){
              $data = [];
              $response = MLAPI::GetSubscribers();
              foreach ($response['body']['data'] as $info){
                  $nestedData['id'] = $info['id'];
                  $nestedData['subscribed_at'] = date('d/m/Y H:i:s', strtotime($info['subscribed_at']));
                  $nestedData['name'] = $info['fields']['name'];
                  $nestedData['email'] = $info['email'];
                  $nestedData['country'] = $info['fields']['country'];

                  $data[] = $nestedData;
              }

              return DataTables::of($data)
                  ->addIndexColumn()
                  ->addColumn('action', function($row){
                      $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm edit-record" data-id="' . $row['id']. '">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm delete-record" data-id="' . $row['id']. '">Delete</a>';
                      return $actionBtn;
                  })
                  ->rawColumns(['action'])
                  ->make(true);
          }
          return view('subscriber-table');
      }else{
          return view('api-key-authentication');
      }

  }

  /**
   * Create or Update a Subscriber
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $subscriberID = $request->id;

    if ($subscriberID) {
      // Attempt to update subscriber
      $response = MLAPI::UpdateSubscriber($subscriberID, $request->name, $request->country);

      // Check if subscriber updated successfully
      if ($response['status_code'] == 200){
        return response()->json("Updated");
      }elseif($response['status_code'] == 422){
        return response()->json(['message' => "invalid data"], 422);
      }
    } else {
      // Attempt to create new subscriber
      $response = MLAPI::NewSubscriber($request->email, $request->name, $request->country);

      // Check if subscriber was created successfully
      if ($response['status_code'] == 200){
        return response()->json(['message' => "already exits"], 422);
      }elseif($response['status_code'] == 422){
        return response()->json(['message' => "invalid data"], 422);
      }else{
        return response()->json("Created");
      }
    }
  }


  /**
   * Get Subscriber data for editing
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $response = MLAPI::ViewSubscriber($id);

    return response()->json($response);
  }

  /**
   * Delete Subscriber
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    // Attempt to delete subscriber
    $response = MLAPI::DeleteSubscriber($id);

    // Check if subscriber was deleted successfully
    if ($response['status_code'] == 204){
      return response()->json("Deleted");
    }elseif($response['status_code'] == 404){
      return response()->json(['message' => "error"], 422);
    }

  }
}
