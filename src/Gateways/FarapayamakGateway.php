<?php

namespace Pamenary\LaravelSms\Gateways;

/**
 * Created by PhpStorm.
 * User: Ali
 * Date: 12/23/2016
 * Time: 12:51 PM
 */
class FarapayamakGateway extends GatewayAbstract {

	/**
	 * FarapayamakGateway constructor.
	 */
	public function __construct() {

		$this->webService  = config('sms.gateway.farapayamak.webService');
		$this->username    = config('sms.gateway.farapayamak.username');
		$this->password    = config('sms.gateway.farapayamak.password');
		$this->from        = config('sms.gateway.farapayamak.from');
	}


	/**
	 * @param array $numbers
	 * @param       $text
	 * @param bool  $isflash
	 *
	 * @return mixed
	 * @internal param $to | array
	 */
	public function sendSMS( array $numbers, $text, $isflash = false ) {
		// Check credit for the gateway
		if(!$this->GetCredit()) return;
		try {
			$client = new \SoapClient( $this->webService );
			$parameters['username'] = $this->username;
			$parameters['password'] = $this->password;
			$parameters['from'] = $this->from;
			$parameters['to'] = $numbers;
			$parameters['text'] = $text;
			$parameters['isflash'] = $isflash;
			$parameters['udh'] = "";
			$parameters['recId'] = array(0);
			$parameters['status'] = 0x0;

			$result = $client->SendSms($parameters)->SendSmsResult;

			return $result;
		} catch( SoapFault $ex ) {
			echo $ex->faultstring;
		}
	}


	/**
	 * @return mixed
	 */
	public function getCredit() {
		if(!$this->username and !$this->password)
			return 'Blank Username && Password';
		try {
			$client = new \SoapClient( $this->webService );

			return $client->GetCredit(array("username" => $this->username, "password" => $this->password))->GetCreditResult;
		} catch( SoapFault $ex ) {
			echo $ex->faultstring;
		}
	}

}