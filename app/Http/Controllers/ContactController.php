<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\oauth_access_token;
use http\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Constants\Constants;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    protected $Constants;

    public function __construct()
    {
        $this->Constants = new Constants();
    }

    protected function changeEnv($data = array())
    {
        if (count($data) > 0) {

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach ((array)$data as $key => $value) {

                // Loop through .env-data
                foreach ($env as $env_key => $env_value) {

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if ($entry[0] == $key) {
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);

            return true;
        } else {
            return false;
        }
    }

    /** Authentication and Refresh Connection Logic -- DO NOT TOUCH ALL IS DYNAMICALLY CODED */
    public function getClientCode()
    {
        return redirect(Constants::authentication_uri);
    }

    public function storeAccessTokens()
    {
        $this->Constants->setCode(request('code'));
        $this->Constants->setScope(request('scope'));
        $response = Http::asForm()->withOptions(['verify' => false]);
        $response = $response->post(Constants::accessToken_uri, [
            'client_id' => Constants::client_id,
            'client_secret' => Constants::client_secret,
            'code' => $this->Constants->getCode(),
            'grant_type' => Constants::grant_type,
            'scope' => $this->Constants->getScope(),
            'redirect_uri' => Constants::redirect_uri
        ]);
        if (oauth_access_token::first())
            oauth_access_token::first()->update($response->json());
        else
            oauth_access_token::create($response->json());
        return redirect('contacts');
    }

    protected function RefreshToken($refresh_token)
    {
        $response = Http::asForm()->withOptions(['verify' => false]);
        $response = $response->withHeaders([
            'Authorization' => 'Basic ' . base64_encode(Constants::client_id . ':' . Constants::client_secret)
        ])->post(Constants::accessToken_uri, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token
        ]);
        if (oauth_access_token::first())
            oauth_access_token::first()->update($response->json());
        else
            oauth_access_token::create($response->json());
    }

    /** Authentication and Refresh Connection Logic -- DO NOT TOUCH ALL IS DYNAMICALLY CODED */
    public function allContacts()
    {
        $tokens = oauth_access_token::first();
        if (!$tokens)
            return $this->getClientCode();
        $responseQuery = Http::asForm()->withOptions(['verify' => false])
            ->withHeaders([
                'Authorization' => 'Bearer ' . $tokens->access_token,
                'Accept' => 'application/json, */*'
            ]);
        $response = $responseQuery->get(Constants::api_uri . '/contacts?optional_properties=fax_numbers');
        if ($response->status() === 401) {
            $this->RefreshToken($tokens->refresh_token);
            return $this->allContacts();
        }

        $getTags = $responseQuery->get(Constants::api_uri . '/tags');

        return view('contacts')->with(['data' => $response->json(),'tags'=>$getTags->json()]);
    }

    public function retrieveContact($contact_id)
    {
        $tokens = oauth_access_token::first();
        if (!$tokens)
            return $this->getClientCode();
        $responseQuery = Http::asForm()->withOptions(['verify' => false])
            ->withHeaders([
                'Authorization' => 'Bearer ' . $tokens->access_token,
                'Accept' => 'application/json, */*'
            ]);
        $response = $responseQuery->get(Constants::api_uri . '/contacts/'. $contact_id.'?optional_properties=fax_numbers' );
        if ($response->status() === 401) {
            $this->RefreshToken($tokens->refresh_token);
            return $this->allContacts();
        }
        if (!empty($response->json()['tag_ids'][0])) {
            $tagDetails = $responseQuery->get(Constants::api_uri . '/tags/' . $response->json()['tag_ids'][0]);
            return ['data'=>$response->json(),'tag'=>$tagDetails->json()];
        }else
            return ['data'=>$response->json(),'tag'=>[]];
    }
    /**
     * @param Request $request
     * @return mixed
     */
    public function createContact(Request $request)
    {
        $tokens = oauth_access_token::first();
        if (!$tokens)
            return $this->getClientCode();
        $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:rfc,dns'],
            'phone' => ['required', 'min:6', 'integer'],
            'fax' => ['required', 'min:6', 'integer']
        ]);
        $contact = [
            'given_name' => request('first_name'),
            'family_name' => request('last_name'),
            'email_addresses' => [
                [
                    "email" => request('email'),
                    "field" => "EMAIL1"
                ]
            ],
            'phone_numbers' => [
                [
                    "number" => request('phone'),
                    "field" => "PHONE1"
                ]
            ],
            'fax_numbers' => [
                [
                    "number" => request('fax'),
                    "field" => "FAX1"
                ]
            ]
        ];
        $responseQuery = Http::withOptions(['verify' => false])
            ->withHeaders([
                'Authorization' => 'Bearer ' . $tokens->access_token,
                'Accept' => 'application/json, */*',
                'Content-Type' => 'application/json'
            ]);
        $response = $responseQuery->post(Constants::api_uri . '/contacts', $contact);
        if ($response->status() === 401) {
            $this->RefreshToken($tokens->refresh_token);
            return $this->createContact($request);
        }
        $applyTag = $responseQuery->post(Constants::api_uri . '/tags/'.request('tag').'/contacts',['ids'=>[$response->json()['id']]]);

        return redirect('contacts');
    }

    public function updateContact(Request $request, $contactId)
    {
        $tokens = oauth_access_token::first();
        if (!$tokens)
            return $this->getClientCode();
        $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:rfc,dns'],
            'phone' => ['required', 'min:6', 'integer'],
            'fax' => ['required', 'min:6', 'integer']
        ]);
        $contact = [
            'given_name' => request('first_name'),
            'family_name' => request('last_name'),
            'email_addresses' => [
                [
                    "email" => request('email'),
                    "field" => "EMAIL1"
                ]
            ],
            'phone_numbers' => [
                [
                    "number" => request('phone'),
                    "field" => "PHONE1"
                ]
            ],
            'fax_numbers' => [
                [
                    "number" => request('fax'),
                    "field" => "FAX1"
                ]
            ]
        ];
        $response = Http::withOptions(['verify' => false]);
        $response = $response->withHeaders([
            'Authorization' => 'Bearer ' . $tokens->access_token,
            'Accept' => 'application/json, */*',
            'Content-Type' => 'application/json'
        ])->patch(Constants::api_uri . '/contacts/' . $contactId, $contact);
        if ($response->status() === 401) {
            $this->RefreshToken($tokens->refresh_token);
            return $this->updateContact($request, $contactId);
        }
        return redirect('contacts');
    }

    public function deleteContact($contactId)
    {
        $tokens = oauth_access_token::first();
        if (!$tokens)
            return $this->getClientCode();
        $response = Http::asForm()->withOptions(['verify' => false]);
        $response = $response->withHeaders([
            'Authorization' => 'Bearer ' . $tokens->access_token,
            'Accept' => 'application/json, */*'
        ])->delete(Constants::api_uri . '/contacts/' . $contactId);
        if ($response->status() === 401) {
            $this->RefreshToken($tokens->refresh_token);
            return $this->deleteContact($contactId);
        }
        return redirect('contacts');
    }
}
