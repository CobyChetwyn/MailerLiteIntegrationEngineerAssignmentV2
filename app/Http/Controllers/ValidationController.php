<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use MailerLite\MailerLite;
use Illuminate\Support\Facades\Redirect;

class ValidationController extends Controller
{

    /**
     * Show form to be completed by user.
     *
     * @return View
     */
    public function showform()
    {
        return view('api-key-authentication');
    }

    /**
     * Validate that form fields have been completed
     * Secondly if they have checked if the API key is valid
     *
     * @return View
     * @throws ValidationException
     */
    public function validateform(Request $request)
    {

        // Validate that the apiKey field has data
        $this->validate($request, [
            'apiKey' => 'required'
        ]);

        $mailerLite = new MailerLite(['api_key' => $request->apiKey]);

        try {
            // Check if the key works in an API call
            $mailerLite->subscribers->get();

            // Store key in database
            Config::updateOrCreate(
                ['id' => 4],
                ['value' => $request->apiKey]
            );

            return Redirect::to('/');
        } catch (Exception) {
            return view('api-key-authentication', [
                'key' => 'invalid'
            ]);
        }

    }
}
