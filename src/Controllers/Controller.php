<?php

namespace crocodicstudio\dokularavel\Controllers;

use Illuminate\Routing\Controller as BaseController;

error_reporting(E_ALL ^ E_NOTICE);
// set_time_limit(200);

use Cache;

class Controller extends BaseController
{    

    //API DOKU ENDPOINT	
	var $prePaymentUrl      = 'http://staging.doku.com/api/payment/PrePayment';
	var $paymentUrl         = 'http://staging.doku.com/api/payment/paymentMip';
	var $directPaymentUrl   = 'http://staging.doku.com/api/payment/PaymentMIPDirect';
	var $generateCodeUrl    = 'http://staging.doku.com/api/payment/doGeneratePaymentCode';
	var $redirectPaymentUrl = 'http://staging.doku.com/api/payment/doInitiatePayment';
	var $captureUrl         = 'http://staging.doku.com/api/payment/DoCapture';
	var $paymentStatusUrl   = 'https://staging.doku.com/Suite/CheckStatus';

	// var $prePaymentUrl      = 'https://pay.doku.com/api/payment/PrePayment';
	// var $paymentUrl         = 'https://pay.doku.com/api/payment/paymentMip';
	// var $directPaymentUrl   = 'https://pay.doku.com/api/payment/PaymentMIPDirect';
	// var $generateCodeUrl    = 'https://pay.doku.com/api/payment/doGeneratePaymentCode';
	// var $redirectPaymentUrl = 'https://pay.doku.com/api/payment/doInitiatePayment';
	// var $captureUrl         = 'https://pay.doku.com/api/payment/DoCapture';
	// var $paymentStatusUrl   = 'https://pay.doku.com/Suite/CheckStatus';

	private function checkLiveMode() {
		if(config('dokularavel.LIVE_MODE') == TRUE) {
			$this->prePaymentUrl      = 'https://pay.doku.com/api/payment/PrePayment';
			$this->paymentUrl         = 'https://pay.doku.com/api/payment/paymentMip';
			$this->directPaymentUrl   = 'https://pay.doku.com/api/payment/PaymentMIPDirect';
			$this->generateCodeUrl    = 'https://pay.doku.com/api/payment/doGeneratePaymentCode';
			$this->redirectPaymentUrl = 'https://pay.doku.com/api/payment/doInitiatePayment';
			$this->captureUrl         = 'https://pay.doku.com/api/payment/DoCapture';
			$this->paymentStatusUrl   = 'https://pay.doku.com/Suite/CheckStatus';
		}
	}

    public function doPrePayment($data){

    	$this->checkLiveMode();

		$data['req_basket'] = $this->formatBasket($data['req_basket']);

		$ch = curl_init( $this->prePaymentUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		Cache::forever('doPrePaymentRaw',date('Y-m-d H:i:s').'--'.$responseJson);

		curl_close($ch);

		return json_decode($responseJson);
	}

	public function doPayment($data){

		$this->checkLiveMode();

		$data['req_basket'] = $this->formatBasket($data['req_basket']);

		$ch = curl_init( $this->paymentUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		Cache::forever('doPaymentRaw',date('Y-m-d H:i:s').'--'.$responseJson);

		curl_close($ch);		

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}

	}

	public function doDirectPayment($data){

		$this->checkLiveMode();

		$ch = curl_init( $this->directPaymentUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		Cache::forever('doDirectPaymentRaw',date('Y-m-d H:i:s').'--'.$responseJson);

		curl_close($ch);

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}

	}

	public function doGeneratePaycode($data){		

		$this->checkLiveMode();		

		$ch = curl_init( $this->generateCodeUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		Cache::forever('doGeneratePaycodeRaw',date('Y-m-d H:i:s').'--'.$responseJson);

		curl_close($ch);

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}

	}

	public function doRedirectPayment($data){

		$this->checkLiveMode();

		$ch = curl_init( $this->redirectPaymentUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($dataPayment));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		Cache::forever('doRedirectPayment',date('Y-m-d H:i:s').'--'.$responseJson);

		curl_close($ch);

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}

	}

	public function doCapture($data){

		$this->checkLiveMode();

		$ch = curl_init( $this->captureUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		Cache::forever('doCapture',date('Y-m-d H:i:s').'--'.$responseJson);

		curl_close($ch);

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}
	}


	public function doCheckPaymentStatus($trans_id){	

		$this->checkLiveMode();

		$data                    = array();
		$data['MALLID']          = config('dokularavel.MALL_ID');
		$data['CHAINMERCHANT']   = 'NA';	
		$data['TRANSIDMERCHANT'] = $trans_id;	
		$data['SESSIONID']       = date('YmdHis');	
		$data['WORDS']           = sha1( $data['MALLID'] . config('dokularavel.SHARED_KEY') . $data['TRANSIDMERCHANT'] );			

		$ch = curl_init( $this->paymentStatusUrl );
		curl_setopt( $ch, CURLOPT_POST, sizeof($data));
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseXML = curl_exec( $ch );

		Cache::forever('doPostCURL',date('Y-m-d H:i:s').'--'.$responseXML);

		curl_close($ch);

		return $responseXML;
	}

	public function doCreateWords($data){
		if(!empty($data['device_id'])){ 
			if(!empty($data['pairing_code'])){
				return sha1($data['amount'] . config('dokularavel.MALL_ID') . config('dokularavel.SHARED_KEY') . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code'] . $data['device_id']);
			}else{
				return sha1($data['amount'] . config('dokularavel.MALL_ID') . config('dokularavel.SHARED_KEY') . $data['invoice'] . $data['currency'] . $data['device_id']);
			}
		}else if(!empty($data['pairing_code'])){
			return sha1($data['amount'] . config('dokularavel.MALL_ID') . config('dokularavel.SHARED_KEY') . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code']);
		}else if(!empty($data['currency'])){
			return sha1($data['amount'] . config('dokularavel.MALL_ID') . config('dokularavel.SHARED_KEY') . $data['invoice'] . $data['currency']);
		}else{
			return sha1($data['amount'] . config('dokularavel.MALL_ID') . config('dokularavel.SHARED_KEY') . $data['invoice']);
		}
	}

	public function doCreateWordsRaw($data){
		if(!empty($data['device_id'])){
			if(!empty($data['pairing_code'])){
				return $data['amount'].config('dokularavel.MALL_ID').config('dokularavel.SHARED_KEY').$data['invoice'].$data['currency'].$data['token'].$data['pairing_code'].$data['device_id'];
			}else{
				return $data['amount'].config('dokularavel.MALL_ID').config('dokularavel.SHARED_KEY').$data['invoice'].$data['currency'].$data['device_id'];
			}
		}else if(!empty($data['pairing_code'])){
			return $data['amount'].config('dokularavel.MALL_ID').config('dokularavel.SHARED_KEY').$data['invoice'].$data['currency'].$data['token'].$data['pairing_code'];
		}else if(!empty($data['currency'])){
			return $data['amount'].config('dokularavel.MALL_ID').config('dokularavel.SHARED_KEY').$data['invoice'].$data['currency'];
		}else{
			return $data['amount'].config('dokularavel.MALL_ID').config('dokularavel.SHARED_KEY').$data['invoice'];
		}
	}

	public function formatBasket($data){
		
		$parseBasket = '';

		if(is_array($data)){
			foreach($data as $basket){
				$parseBasket = $parseBasket . $basket['name'] .','. $basket['amount'] .','. $basket['quantity'] .','. $basket['subtotal'] .';';
			}
		}else if(is_object($data)){
			foreach($data as $basket){
				$parseBasket = $parseBasket . $basket->name .','. $basket->amount .','. $basket->quantity .','. $basket->subtotal .';';
			}
		}else{
			$parseBasket = $data;
		}

		return $parseBasket;
	}
}
