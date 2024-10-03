<?php

namespace App\Http\Middleware;

use Closure;

use App\Models\ClientSetting;
use Log;
use GuzzleHttp\Client;
use Exception;
use App\Models\Service\TenantDatabase;
use App\Models\Service\TenantDomain;

class ServiceAuthorization
{

    private $tenantDatabase;
    private $serviceDatabase;

    private $request;
    private $next;

    private $UnauthorizedResponse='This service has been temporarily disabled for you.';

    public $baseUri;
    public $secret;



    public function __construct()
    {
        $this->database = new TenantDatabase();
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

     public function unauthorizedResponse(){
       $data['0']['code']='0001';
            $data['0']['status']='401'; //same code as in responser()->json() method;
            $data['0']['title']='Unauthorized request';
            $data['0']['detail']='These credentials do not match our records';
            return ['errors'=>$data];
            // return response()->json(, 401);
     }
    public function handle($request, Closure $next)
    {
        $this->next=$next;
        $this->request=$request;

        if(config('customServices.app_log')){
            Log::info("Service Authorization Handle Method Start");
        }

        if(strpos($request->url(), 'service') !== false)
        {
            // Log::info($request->url());
            return $next($request);
        }
        // Handle Request from Api Gateway
        if($request->has('app')){

            if(config('customServices.app_log')){
                Log::info('request From api gateway & request have /"app/" varibale');
            }
          return  $this->authenticateRequest($request)? $next($request): response($this->unauthorizedResponse(), 401);
        }
        // Handle Request from Tenant
        else
        {
            if(config('customServices.app_log')){
                Log::info('request From Tenant');
                Log::info($request->bearerToken());

            }
            return  $this->setDatabase($request->bearerToken())? $next($request): response($this->unauthorizedResponse(), 401);
        }


    }

    public function authenticateRequest($request ){
        if(config('customServices.app_log')){
            Log::info('From Gateway=> authentication request start');
        }

        // Log::info('authentication request start');
        $url='';
        $subdomain=TenantDomain::select('subdomain')->where('app_uuid',$request->app)->first();
        if(config('customServices.app_log')){
                Log::info($subdomain);
        }

        // if(env('APP_ENV')=='local'){
        if(config('customServices.app_env')=='local'){
        $url='http://'.$subdomain['subdomain'];
            // $url='http://127.0.0.1:8014';
        }
        if(config('customServices.app_env')=='production'){
        // if(env('APP_ENV')=='production'){
            $url='http://'.$subdomain['subdomain'].'.'.config('customServices.app_domain');
        }
        if(config('customServices.app_env')=='secure_production'){
        // if(env('APP_ENV')=='secure_production'){
            $url='https://'.$subdomain['subdomain'].'.'.config('customServices.app_domain');
        }

        $this->baseUri=$url;
        $this->secret=$request->header('Authorization');

        if(config('customServices.app_log')){
            Log::info('Tenant url');
            Log::info($url);
            Log::info('From Gateway=> authentication request END');
        }

        $client = new Client(['base_uri' => $this->baseUri]);

        if (isset($this->secret)) {
            $headers['Authorization'] = $this->secret;
        }
            $headers['Accept'] = 'application/json';
        // try {
            if(config('customServices.app_log')){
                    Log::info("user authentication start");
            }

            $response = $client->request('GET', '/api/validate-token/service', ['query' => ['app'=>$request->app],'headers' => $headers]);
            if(config('customServices.app_log')){
                    Log::info("user authentication response");
            }
            $this->setDatabase($request->app);
            if(config('customServices.app_log')){
                    Log::info("user authentication end");
            }

            return true;
        // }
        // catch( Exception $e){
        //   if(config('customServices.app_log')){
        //           Log::info("user authentication catche");
        //   }
        //
        //     return false;
        // }
    }

    public function setDatabase($app_id){
        $database=$this->database->getDatabaseConf($app_id);
        if(config('customServices.app_log')){
            Log::info($database);
            Log::info('set database');
        }

        if($database)
        {
            if(config('customServices.app_log')){
                Log::info('databse true');
            }

            config(['database.connections.mysql.database'=>$database['client_db_database']]);
            return true;
        }
        else
        {
            if(config('customServices.app_log')){
                Log::info('database false ');
            }

            return false;
        }


    }
}
