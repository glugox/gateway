<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Request;
use App\Exceptions\UnableToExecuteRequestException;
use App\Exceptions\ServiceNotFoundException;

/**
 * Class RestClient
 * @package App\Services
 */
class RestClient
{
    /**
     * @var Client
     */
    protected $client;


    /**
     * @var array
     */
    protected $guzzleParams = [
        'headers' => [],
        'timeout' => 40
    ];

    /**
     * @var int
     */
    const USER_ID_ANONYMOUS = -1;

    /**
     * RestClient constructor.
     * @param Client $client
     * @param Request $request
     */
    public function __construct(Client $client, Request $request)
    {
        $this->client = $client;
        $this->injectHeaders($request);
    }

    /**
     * @param Request $request
     */
    private function injectHeaders(Request $request)
    {


        $this->setHeaders(
            [
                'X-User' => $request->user()->id ?? self::USER_ID_ANONYMOUS,
                'X-Client-Ip' => $request->getClientIp(),
                'User-Agent' => $request->header('User-Agent'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        );
        $this->setHeaders($request->header());
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as  $key => $value) {
            $this->guzzleParams['headers'][$key] = $value;
        }
    }


    /**
     * @param string $query
     */
    public function setQuery( string $query ){
        $this->guzzleParams['query'] = $query;
    }

    /**
     * @param $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->guzzleParams['headers']['Content-Type'] = $contentType;

        return $this;
    }

    /**
     * @param $contentSize
     * @return $this
     */
    public function setContentSize($contentSize)
    {
        $this->guzzleParams['headers']['Content-Length'] = $contentSize;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->guzzleParams['headers'];
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->guzzleParams['body'] = $body;

        return $this;
    }

    /**
     * @param array $files
     * @return $this
     */
    public function setFiles($files)
    {
        // Get rid of everything else
        $this->setHeaders(array_intersect_key($this->getHeaders(), ['X-User' => null, 'X-Token-Scopes' => null]));

        if (isset($this->guzzleParams['body'])) unset($this->guzzleParams['body']);

        $this->guzzleParams['timeout'] = 20;
        $this->guzzleParams['multipart'] = [];

        foreach ($files as $key => $file) {
            $this->guzzleParams['multipart'][] = [
                'name' => $key,
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName()
            ];
        }

        return $this;
    }

    /**
     * @param array $parametersJar
     * @return PsrResponsef
     * @throws UnableToExecuteRequestException
     */
    public function syncRequest($serviceKey, $resource, $action, $method='GET', $queryString='')
    {
        
        $service = \App\Service::where('key', '=', $serviceKey)->first();
        if(!$service){
            throw new ServiceNotFoundException('Service '. $serviceKey. ' not found');
        }
        
        $url = $service->base_url . '/' .$resource;

        if(!empty($queryString)){
            $this->setQuery($queryString);
        }
        
        try {
            $method = strtolower($method);
            $response = $this->client->$method( $url, $this->guzzleParams );
        } catch (ConnectException $e) {
            throw new UnableToExecuteRequestException();
        } catch (RequestException $e) {
            return $e->getResponse();
        }

        return $response;
    }


    /**
     * @param Collection $batch
     * @param $parametersJar
     * @return RestBatchResponse
     */
    public function asyncRequest(Collection $batch, $parametersJar)
    {
        $wrapper = new RestBatchResponse();
        $wrapper->setCritical($batch->filter(function($action) { return $action->isCritical(); })->count());

        $promises = $batch->reduce(function($carry, $action) use ($parametersJar) {
            $method = strtolower($action->getMethod());
            $url = $this->buildUrl($parametersJar);
            $carry[$action->getAlias()] = $this->client->{$method . 'Async'}($url, $this->guzzleParams);
            return $carry;
        }, []);

        return $this->processResponses(
            $wrapper,
            collect(Promise\settle($promises)->wait())
        );
    }

    /**
     * @param RestBatchResponse $wrapper
     * @param Collection $responses
     * @return RestBatchResponse
     */
    private function processResponses(RestBatchResponse $wrapper, Collection $responses)
    {
        // Process successful responses
        $responses->filter(function ($response) {
            return $response['state'] == 'fulfilled';
        })->each(function ($response, $alias) use ($wrapper) {
            $wrapper->addSuccessfulAction($alias, $response['value']);
        });

        // Process failures
        $responses->filter(function ($response) {
            return $response['state'] != 'fulfilled';
        })->each(function ($response, $alias) use ($wrapper) {
            $response = $response['reason']->getResponse();
            if ($wrapper->hasCriticalActions()) throw new UnableToExecuteRequestException($response);

            // Do we have an error response from the service?
            if (! $response) $response = new PsrResponse(502, []);
            $wrapper->addFailedAction($alias, $response);
        });

        return $wrapper;
    }

    
    /**
     * @param string $url
     * @param array $params
     * @param string $prefix
     * @return string
     */
    private function injectParams($url, array $params, $prefix = '')
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $url = $this->injectParams($url, $value, $prefix . $key . '.');
            }

            if (is_string($value) || is_numeric($value)) {
                $url = str_replace("{" . $prefix . $key . "}", $value, $url);
            }
        }

        return $url;
    }

}
