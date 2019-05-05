<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\DataFormatException;
use App\Exceptions\NotImplementedException;

use App\Helpers\RestClient;
use App\Presenters\JSONPresenter;


class ForwardController extends Controller
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
    
    public function forward(Request $request, RestClient $client, $service, $resource='', $action='index')
    {
        $resource = $resource == '' ? $service : $resource;
        
        return $this->simpleRequest($request, $client, $service, $resource, $action);
    }
    

    /**
     * @param Request $request
     * @param RestClient $client
     * @return Response
     * @throws NotImplementedException
     */
    private function simpleRequest(Request $request, RestClient $client, $service, $resource, $action)
    {

        $client->setBody($request->getContent());
        if (count($request->allFiles()) !== 0) {
            $client->setFiles($request->allFiles());
        }

        $qs = $request->getQueryString();

        /** @var Debug only */
        //$qs = empty($qs) ? '?XDEBUG_SESSION_START=PHPSTORM' : '&XDEBUG_SESSION_START=PHPSTORM';


        $response = $client->syncRequest($service, $resource, $action, $request->getMethod(), $qs);
        
        return $this->presenter->format((string)$response->getBody(), $response->getStatusCode());
    }
}
