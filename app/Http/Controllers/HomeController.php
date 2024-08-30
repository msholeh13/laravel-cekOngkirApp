<?php

namespace App\Http\Controllers;

use App\City;
use App\Courier;
use App\Province;
use Illuminate\Http\Request;
// use Kavist\RajaOngkir\RajaOngkir;
use Kavist\RajaOngkir\Facades\RajaOngkir;
use LDAP\Result;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        // dd(RajaOngkir::provinsi()->find(12));

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // dd(json_decode(file_get_contents(base_path('database/kota.json'))));

        $province = $this->getProvince();
        $courier = $this->getCourier();

        return view('home', compact('province', 'courier'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $courier = $request->input('courier');


        if ($courier) {
            $data = [
                'origin'        => $this->getCity($request->origin_city),
                'destination'   => $this->getCity($request->destination_city),
                'weight'        => 1300,
                'result'        => []
            ];

            foreach ($courier as $row) {
                $ongkir = RajaOngkir::ongkosKirim([
                    'origin'        => $request->origin_city,
                    'destination'   => $request->destination_city,
                    'weight'        => $data['weight'],
                    'courier'       => $row
                ])->get();

                $data['result'][] = $ongkir;
            }

            // dd($data);

            return view('costs')->with($data);
        } else {
            return redirect()->back();
        }


        // return $result;
    }

    public function getProvince()
    {
        return Province::pluck('title', 'code');
    }

    public function getCity($code)
    {
        return City::where('code', $code)->first();
    }

    public function getCities($id)
    {
        return City::where('province_code', $id)->pluck('title', 'code');
    }

    public function getCourier()
    {
        return Courier::all();
    }

    public function searchCities(Request $request)
    {
        $search = $request->search;

        if (empty($search)) {
            $cities = City::orderBy('title', 'ASC')
                ->select('code', 'title')
                ->limit(5)
                ->get();
        } else {
            $cities = City::orderBy('title', 'ASC')
                ->where('title', 'LIKE', '%' . $search . '%')
                ->select('code', 'title')
                ->limit(5)
                ->get();
        }

        $response = [];

        foreach ($cities as $city) {
            $response[] = [
                'id' => $city->code,
                'text' => $city->title,
            ];
        }

        return json_encode($response);
    }
}
