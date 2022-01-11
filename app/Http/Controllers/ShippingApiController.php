<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShippingResource;
use App\Models\Shipping;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ShippingApiController extends Controller
{
    private $client;
    private $shippingCharge;

    public function __construct()
    {
        $this->shippingCharge = 10;
        $this->client = new Client(['verify'=>false]);
        $this->middleware('api_auth');
    }

    public function getShippingList(Request $request)
    {
        $api_token = $request->input('token');

        $user = User::getUser($api_token);

        return ShippingResource::collection($user->shippings()->get());
    }

    public function addShipping(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'description' => 'string|required',
            'origin' => 'string|required',
            'destination' => 'string|required',
            'weight' => 'numeric|required'
        ]);

        if($validator->fails()){
            return response()->json([
                "message"=>"Wrong data"
            ],500);
        }

        $api_token = $request->input('token');

        $origin = $request->input('origin');
        $destination = $request->input('destination');
        $response = json_decode(
            $this->client->get('https://maps.googleapis.com/maps/api/distancematrix/json'.
                '?departure_time=now'.
                '&destinations='.urlencode($destination).
                '&origins='.urlencode($origin).
                '&key=AIzaSyDxczA6T86uhMRV4W30sLOwZd78-2Hmodw'
            )->getBody()->getContents(),
            true);
        $distance = $response['rows'][0]['elements'][0]['distance']['value'];

        $user = User::getUser($api_token);

        $shipping = new Shipping();
        $shipping->user_id = $user->id;
        $shipping->description = $request->input('description');
        $shipping->origin = $origin;
        $shipping->destination = $destination;
        $shipping->weight = $request->input('weight');
        $shipping->distance = $distance;
        $shipping->price = $distance/1000 * $shipping->weight * $this->shippingCharge;//distance in meter
        $shipping->save();

        return new ShippingResource($shipping);

    }

    public function calculatePrice(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'description' => 'string|required',
            'origin' => 'string|required',
            'destination' => 'string|required',
            'weight' => 'numeric|required'
        ]);

        if($validator->fails()){
            return response()->json([
                "message"=>"Wrong data"
            ],500);
        }
        $origin = $request->input('origin');
        $destination = $request->input('destination');
        $response = json_decode(
            $this->client->get('https://maps.googleapis.com/maps/api/distancematrix/json'.
                '?departure_time=now'.
                '&destinations='.urlencode($destination).
                '&origins='.urlencode($origin).
                '&key=AIzaSyDxczA6T86uhMRV4W30sLOwZd78-2Hmodw'
            )->getBody()->getContents(),
            true);
        $distance = $response['rows'][0]['elements'][0]['distance']['value'];
        $weight = $request->input('weight');
        return response()->json([
            'distance' => $distance,
            'weight' => $weight,
            'price' =>  $distance*$weight*$this->shippingCharge
        ]);


    }
}
