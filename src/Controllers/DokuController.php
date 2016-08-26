<?php 
namespace crocodicstudio\dokularavel\Controllers;

use Request;
use DokuLaravel;
use Session;
use Cache;

class DokuController extends Controller {

	var $payment_channel = '15';

	public function index() {						
		
		$amount = intval(Request::get('amount'));
		$amount = ($amount)?:rand(100000,900000);
		$amount .= '.00';

		$trans_id = 'tandamata_'.time();		

		$params = array(
			'amount'   => $amount,
			'invoice'  => $trans_id,
			'currency' => '360'
		);		
			
		$doku               = new DokuLaravel;		
		$data['shared_key'] = config('dokularavel.SHARED_KEY');
		$data['mall_id']    = config('dokularavel.MALL_ID');
		$data['words']      = $doku->doCreateWords($params);
		$data['amount']     = $amount;
		$data['invoice']    = $trans_id;
		$data['currency']   = $params['currency'];
		$data['payment_channel'] = $this->payment_channel;

		$data['doPrePayment'] = Cache::get('doPrePayment');
		$data['doPayment']    = Cache::get('doPayment');

		Session::put('doku_amount',$amount);
		Session::put('doku_invoice',$trans_id);		
		
		return view('dokularavel::payment_form',$data);
	}

	public function pay() {
		$faker = \Faker\Factory::create('id_ID');


		$token            = Request::get('doku_token');
		$pairing_code     = Request::get('doku_pairing_code');
		$invoice_no       = Request::get('doku_invoice_no');
		$amount           = Request::get('doku_amount');
		$currency         = Request::get('doku_currency');	
		$chain 			  = Request::get('doku_chain_merchant');
		
		$customer_name    = preg_replace('/[^a-zA-Z]+/', '', $faker->name);
		$customer_phone   = preg_replace('/\D/', '', $faker->phoneNumber);
		$customer_email   = $faker->email;
		$customer_address = $faker->address;	


		$params = array(
			'amount'       => $amount,
			'invoice'      => $invoice_no,
			'currency'     => $currency,
			'pairing_code' => $pairing_code,
			'token'        => $token
		);

		$doku  = new DokuLaravel;	

		$words = $doku->doCreateWords($params);

		$basket[] = array(
			'name'     => 'Invoice For Order No. '.$invoice_no,
			'amount'   => $amount,
			'quantity' => '1',
			'subtotal' => $amount
		);

		$customer = array(
			'name'         => $customer_name,
			'data_phone'   => $customer_phone,
			'data_email'   => $customer_email,
			'data_address' => $customer_address
		);

		$data = array(
			'req_token_id'     => $token,
			'req_pairing_code' => $pairing_code,
			'req_customer'     => $customer,
			'req_basket'       => $basket,
			'req_words'        => $words
		);

		$responsePrePayment = $doku->doPrePayment($data);

		Cache::put('doPrePayment',$invoice_no.':'.json_encode($responsePrePayment), 30);

		if($responsePrePayment->res_response_code == '0000'){
			//prepayment success
			$dataPayment = array(
				'req_mall_id'           => config('dokularavel.MALL_ID'),
				'req_chain_merchant'    => $chain,
				'req_amount'            => $amount,
				'req_words'             => $words,
				'req_words_raw' 		=> $doku->doCreateWordsRaw($params),
				'req_purchase_amount'   => $amount,
				'req_trans_id_merchant' => $invoice_no,
				'req_request_date_time' => date('YmdHis'),
				'req_currency'          => $currency,
				'req_purchase_currency' => $currency,
				'req_session_id'        => sha1(date('YmdHis')),
				'req_name'              => $customer_name,
				'req_payment_channel'   => $this->payment_channel,
				'req_basket'            => $basket,
				'req_mobile_phone'		=> str_limit($customer_phone,12,''),
				'req_email'             => $customer_email,
				'req_token_id'          => $token, 
				'req_address' 			=> $customer_address 
			);
			$result = $doku->doPayment($dataPayment); 

			Cache::put('doPayment',$invoice_no.':'.json_encode($result), 30);

			if($result->res_response_code == '0000'){
				 //redirect process to doku
		        $result->res_redirect_url   = Route('DokuController.finish',['invoice_no'=>$invoice_no,'amount'=>$amount]);
		        $result->res_show_doku_page = false; //true if you want to show doku page first before redirecting to redirect url		        

				echo json_encode($result);
			}else{
				echo json_encode($result);
			}
		}else{
			echo json_encode($result);
		}
	}

	public function finish($invoice_no,$amount) {
		$data['doku_amount'] = $amount;
		$data['doku_invoice'] = $invoice_no;
		return view('dokularavel::finish',$data);
	}
}