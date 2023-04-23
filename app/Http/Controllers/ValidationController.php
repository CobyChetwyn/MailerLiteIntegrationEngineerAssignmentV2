<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use MailerLite\MailerLite;
use Illuminate\Support\Facades\Redirect;

class ValidationController extends Controller {

    public function showform() {
        return view('api-key-authentication');
    }

    public function validateform(Request $request) {

        // Validate that the apiKey field has data
        $this->validate($request,[
            'apiKey'=>'required'
        ]);

        $mailerLite = new MailerLite(['api_key' => $request->apiKey]);

        try{
            // Check if the key works in an API call
            $mailerLite->subscribers->get();

            // Store key in database
            Config::updateOrCreate(
                ['id' => 4],
                ['value' => $request->apiKey]
            );

            return Redirect::to('/');
        } catch(\Exception $e) {
            return view('api-key-authentication', [
                'key' => 'invalid'
            ]);
        }

    }
}
