<?php 
namespace App\Http\Controllers;

use DB;

class DokuLaravelHookController extends Controller {
	
	/* 
	| ---------------------------------------------------------
	| beforePayment will be execute before payment transaction is execute
	| ---------------------------------------------------------	
	| array $data
	|	the essential keys : 
	|		amount
	|		invoice
	|		currency
	|		payment_channel
	|
	*/
	public function beforePayment($data) {
		//Start code after this line
		
	}


	/* 
	| ---------------------------------------------------------
	| afterPayment will be execute after payment transaction is done 
	| ---------------------------------------------------------
	| boolean $status 
	| array $dataPayment
	| 	the essential keys : 
	|		req_mall_id, 
	|		req_amount
	|		req_trans_id_merchant
	|		req_request_date_time
	|		req_currency
	|		req_name
	|		req_payment_channel
	|		req_basket
	|		req_mobile_phone
	|		req_email
	|		req_address
	|
	*/
	public function afterPayment($status,$dataPayment) {
		//Start code after this line		

	}

}