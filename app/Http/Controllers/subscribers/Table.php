<?php

namespace App\Http\Controllers\subscribers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\api\MLAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Config;
use DataTables;

class Table extends Controller
{
    /**
     * Get data for table.
     *
     * @return Response
     */
    public function index()
    {
        $apiKey = Config::select('value')->where('type', 'apiKey')->first();

        if ( ! is_null($apiKey->value)) {
            if (\request()->ajax()) {
                $data     = [];
                $response = MLAPI::GetSubscribers();
                foreach ($response['body']['data'] as $info) {
                    $nestedData['id']            = $info['id'];
                    $nestedData['subscribed_at'] = date('d/m/Y H:i:s',
                        strtotime($info['subscribed_at']));
                    $nestedData['name']          = $info['fields']['name'];
                    $nestedData['email']         = $info['email'];
                    $nestedData['country']       = $info['fields']['country'];

                    $data[] = $nestedData;
                }

                return DataTables::of($data)
                                 ->addIndexColumn()
                                 ->editColumn('email',
                                     '<a href="javascript:void(0)" class="edit-record" data-id="{{$id}}">{{$email}}</a>')
                                 ->addColumn('action', function ($row) {
                                     return '<a href="javascript:void(0)" class="edit btn btn-success btn-sm edit-record" data-id="'
                                            . $row['id']
                                            . '">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm delete-record" data-id="'
                                            . $row['id'] . '">Delete</a>';
                                 })
                                 ->rawColumns(['action', 'email'])
                                 ->make(true);
            }

            return view('subscriber-table');
        } else {
            return view('api-key-authentication');
        }

    }

    /**
     * Create or Update a Subscriber
     *
     * @param   Request  $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $subscriberID = $request->id;

        if ($subscriberID) {
            // Attempt to update subscriber
            $response = MLAPI::UpdateSubscriber($subscriberID, $request->name,
                $request->country);

            // Check if subscriber updated successfully
            if ($response['status_code'] == 200) {
                return response()->json("Updated");
            } elseif ($response['status_code'] == 422) {
                return response()->json(['message' => "invalid data"], 422);
            }
        } else {
            // Attempt to create new subscriber
            $response = MLAPI::NewSubscriber($request->email, $request->name,
                $request->country);

            // Check if subscriber was created successfully
            if ($response['status_code'] == 200) {
                return response()->json(['message' => "already exits"], 422);
            } elseif ($response['status_code'] == 422) {
                return response()->json(['message' => "invalid data"], 422);
            } else {
                return response()->json("Created");
            }
        }
    }


    /**
     * Get Subscriber data for editing
     *
     * @param   int  $id
     *
     * @return JsonResponse
     */
    public function edit(int $id)
    {
        $response = MLAPI::ViewSubscriber($id);

        return response()->json($response);
    }

    /**
     * Delete Subscriber
     *
     * @param   int  $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        // Attempt to delete subscriber
        $response = MLAPI::DeleteSubscriber($id);

        // Check if subscriber was deleted successfully
        if ($response['status_code'] == 204) {
            return response()->json("Deleted");
        } elseif ($response['status_code'] == 404) {
            return response()->json(['message' => "error"], 422);
        }

    }
}
