<?php

require 'vendor/autoload.php';
use GuzzleHttp\Client;

class XlRequest {
	
	private $imei; 
	
	private $msisdn;
	
	private $client;
	
	private $header;
	
	private $session;
	
	private $date;
	
	public function __construct() {
		
		$this->client =new Client(['base_uri' => 'https://xclite.netlify.app']); 
		
		$this->imei = '3030912666'; 
		
		$this->date = date('Ymdhis');
		
		$this->header=array (
			"Host:xclite.netlify.app",
	                "user-agent:Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36",
	                "content-type:application/json",
	                "origin:https://xclite.netlify.app",
	                "sec-fetch-site:same-origin",
	                "sec-fetch-mode:cors",
	                "sec-fetch-dest:empty",
	                "referer:https://xclite.netlify.app/"
		);
	}
	public function login($msisdn, $passwd) {
		
	    $payload = array (
	            'Body' => array (
	                    'Header' => array(
	                            'IMEI' => $this->imei,
	                            'ReqID' => substr($this->date, 11),
	                    ),
	                    'LoginV2Rq' => array(
	                        'msisdn' => $msisdn,
	                        'pass' => $passwd,
	                    )
	             ),
	            'onNet' => 'True',
	            'sessionId' => null,
	            'staySigned' => 'False',
	            'platform' => '00',
	            'onNetLogin' => 'YES',
	            'appVersion' => '3.0.1',
	            'sourceName' => 'Android',
	           'sourceVersion'=> '7.1.2'
	   );
	   try {

			$response = $this->client->post('/api/users/login',
				[
					'debug' => FALSE,
					'json' => $payload,
					'headers' => $this->header
				]
			);
			$body = json_decode($response->getBody());
			if ($body->responseCode === '00') {
			    return $body->sessionId;
			}
            return false;
		}
		catch (Exception $e) {
			return $e;
		}
	}
	
	public function getPass($msisdn) {
		
		$payload = array (
						'Body'=> array (
							'Header'=> array (
								'ReqID'=>substr($this->date, 10),
								'IMEI'=>$this->imei
								),
							'ForgotPasswordRq'=> array (
								'msisdn'=>$msisdn,
								'username'=>''
							)
						),
						'sessionId'=>null
				);
				
				try {
					$response = $this->client->post('/api/users/otp',[
						'debug' => FALSE,
						'json' => $payload,
						'headers' => $this->header
				  ]);
				  $body = json_decode($response->getBody());
				  return $body;
				}
				catch(Exception $e) {}
				
	}
	public function register($msisdn, $serviceID, $session) {
	 
	   $payload = array (
					'Body'=> array (
								'HeaderRequest' => array (
								    'applicationID'=> '3',
								    'applicationSubID'=> '1',
								    'touchpoint' => 'MYXL',
								    'requestID' => substr($this->date, 11),
								    'msisdn' => $msisdn,
								    'serviceID' => $serviceID
					            ),
					            'opPurchase'=> array (
								    'msisdn' => $msisdn,
								    'serviceid' => $serviceID
					             ),
					            'Header' => array (
								    'IMEI'=> $this->imei,
								    'ReqID' => substr($this->date, 10)
					        )
				    ),
				    'sessionId'=> $session,
				    'onNet'=> 'True',
				    'platform'=> '00',
				    'staySigned'=>'Yes',
				    'appVersion'=>'3.0.1',
				    'sourceName'=>'Android',
				    'sourceVersion'=> '7.1.1'
		 );
		try {
			$response = $this->client->post('/api/users/buy',[
					'debug' => FALSE,
					'json' => $payload,
					'headers' => $this->header
			]);
			$status = json_decode((string) $response->getBody());
			
			if (isset($status->responseCode)) { return $status; }
			
			else {
			    return TRUE;
			}
		}
		catch(Exception $e) {}
	}
	
}
?>
