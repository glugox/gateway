<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\NotImplementedException;
use App\Helpers\RestClient;
use App\Presenters\JSONPresenter;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Setting;


class SettingsController extends Controller
{
    
    
    private $presenter;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JSONPresenter $presenter)
    {
        $this->presenter = $presenter;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){


        $collection = JsonResource::collection(Setting::all());
        $grouped = $collection->groupBy(['module', 'config_key'])->map(function ($value) {
            return collect($value)->map(function($configKeyArr){
                return $configKeyArr[0]; // revert config key array to just an element object as it is unique. This happened while grouping.
            });
        });
        return [
            'data' => $grouped
        ];
    }


}
