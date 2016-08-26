<?php 
namespace crocodicstudio\dokularavel\Facades;

error_reporting(E_ALL ^ E_NOTICE);

use Cache;

class DokuLaravel {

	//API DOKU ENDPOINT
	var $prePaymentUrl      = 'http://staging.doku.com/api/payment/PrePayment';
	var $paymentUrl         = 'http://staging.doku.com/api/payment/paymentMip';
	var $directPaymentUrl   = 'http://staging.doku.com/api/payment/PaymentMIPDirect';
	var $generateCodeUrl    = 'http://staging.doku.com/api/payment/doGeneratePaymentCode';
	var $redirectPaymentUrl = 'http://staging.doku.com/api/payment/doInitiatePayment';
	var $captureUrl         = 'http://staging.doku.com/api/payment/DoCapture';

	var $sharedKey; //doku's merchant unique key
	var $mallId; //doku's merchant id	

	public function __construct() {
		$this->sharedKey = config('dokularavel.SHARED_KEY');
		$this->mallId	 = config('dokularavel.MALL_ID');
	}

	public function doPrePayment($data){

		$data['req_basket'] = $this->formatBasket($data['req_basket']);

		$ch = curl_init( $this->prePaymentUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		curl_close($ch);

		return json_decode($responseJson);
	}

	public function doPayment($data){

		$data['req_basket'] = $this->formatBasket($data['req_basket']);

		$ch = curl_init( $this->paymentUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		curl_close($ch);

		Cache::put('doPaymentRaw',$responseJson, 30);

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}

	}

	public function doDirectPayment($data){

		$ch = curl_init( $this->directPaymentUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		curl_close($ch);

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}

	}

	public function doGeneratePaycode($data){

		$ch = curl_init( $this->generateCodeUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		curl_close($ch);

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}

	}

	/* 
	|--------------------------------------------------------------------
	| Create Payment 
	|--------------------------------------------------------------------
	| $data = parameters array
	| param = amount, trans_id, product_name
	|
	*/
	public function doRedirectPayment($data){

		$ch = curl_init( $this->redirectPaymentUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($dataPayment));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		curl_close($ch);

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}

	}

	public function doCapture($data){

		$ch = curl_init( $this->captureUrl );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$responseJson = curl_exec( $ch );

		curl_close($ch);

		if(is_string($responseJson)){
			return json_decode($responseJson);
		}else{
			return $responseJson;
		}

	}

	public function doCreateWords($data){

		if(!empty($data['device_id'])){ 

			if(!empty($data['pairing_code'])){

				return sha1($data['amount'] . $this->mallId . $this->sharedKey . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code'] . $data['device_id']);

			}else{

				return sha1($data['amount'] . $this->mallId . $this->sharedKey . $data['invoice'] . $data['currency'] . $data['device_id']);

			}

		}else if(!empty($data['pairing_code'])){

			return sha1($data['amount'] . $this->mallId . $this->sharedKey . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code']);

		}else if(!empty($data['currency'])){

			return sha1($data['amount'] . $this->mallId . $this->sharedKey . $data['invoice'] . $data['currency']);

		}else{

			return sha1($data['amount'] . $this->mallId . $this->sharedKey . $data['invoice']);

		}
	}

	public function doCreateWordsRaw($data){

		if(!empty($data['device_id'])){

			if(!empty($data['pairing_code'])){

				return $data['amount'] . $this->mallId . $this->sharedKey . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code'] . $data['device_id'];

			}else{

				return $data['amount'] . $this->mallId . $this->sharedKey . $data['invoice'] . $data['currency'] . $data['device_id'];

			}

		}else if(!empty($data['pairing_code'])){

			return $data['amount'] . $this->mallId . $this->sharedKey . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code'];

		}else if(!empty($data['currency'])){

			return $data['amount'] . $this->mallId . $this->sharedKey . $data['invoice'] . $data['currency'];

		}else{

			return $data['amount'] . $this->mallId . $this->sharedKey . $data['invoice'];

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

} //END CLASS