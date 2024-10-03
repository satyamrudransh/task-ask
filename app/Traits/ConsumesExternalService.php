<?php

namespace App\Traits;
use Log;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
// use GuzzleHttp\Stream;
use GuzzleHttp\Psr7;
trait ConsumesExternalService
{
    /**
     * Send a request to any service
     * @return string
     */
       // $requestDataMode is a mode by with data is send during request in guzzle
       // e.g.-query parameter(Param), (2)-form-param
    // public function performRequest($method, $requestUrl,$requestDataMode=null, $formParams = [], $headers = [])
    public function performRequest($method, $requestUrl, $formParams = [], $headers = [])
    {

        $request = new Request();

        $client = new Client();
        // if (isset($this->secret)) {
        //     $headers['Authorization'] = $this->secret;
        // }

        if(config('app.log')){
            Log::info("Consume External Service, performRequest Start");
            // Log::info($this->baseUri);
            Log::info($method);
            Log::info($requestUrl);
            Log::info($formParams);
        }

        if($method=='GET')
        {
        $response = $client->request($method, $requestUrl, ['query' => $formParams, 'headers' => $headers]);

        }

        elseif(array_key_exists("file",$formParams)||array_key_exists("image",$formParams)||array_key_exists("sectionBackgroundImage",$formParams)||array_key_exists("experienceImage",$formParams)||array_key_exists("video",$formParams)||array_key_exists("backgroundImage",$formParams)) {
            foreach ($formParams as $name => $value) {
              if($name=='file'||$name=='image'||$name=='sectionBackgroundImage'||$name=='experienceImage'||$name=='video'||$name=='backgroundImage')
              {
                $multipart[] = [
                        'name' => $name,
                        'contents' => fopen($value, 'r'),
                        'filename' => $value->getClientOriginalName(),
                    ];
              }
              elseif($name=='experienceType'||$name=='practiceType'||$name=='technologyType'||$name=='industryType'||$name=='locationType')
              {
                foreach($value as $val){
                  $i=0;
                    $multiName = $name . '[' .$i . ']' . (is_array($val) ? '[' . key($val) . ']' : '' ) . '';
                    $multipart[] = [
                      'name' => $multiName, 
                      'contents' => (is_array($val) ? reset($val) : $val)];
                      $i++;
                }
              }
               else
              {
                $multipart[] = [
                        'name' => $name,
                        'contents' => $value
                    ];
              }
              
            }
        $response = $client->request($method, $requestUrl, ['headers' => $headers,
            'multipart' =>$multipart,
            ]);
        }

        else
        {
        $response = $client->request($method, $requestUrl, ['form_params' => $formParams, 'headers' => $headers]);

        }
        // if(property_exists($this,'responseHeader')){
        //   $this->responseHeader=$response->getHeader('Content-Type');
        // }
        // if(config('app.log')){
        //   Log::info("Api Response");
        //   $response=$response->getBody()->getContents();
        //   Log::info($response);
        //   Log::info("Consume External Service, performRequest End");
        // }else{
        //   $response=$response->getBody()->getContents();
        // }


        return $response;
        // return $response->getBody()->getContents();
    }
}
