<?php 
namespace crocodicstudio\dokularavel\Controllers;

use Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class DokuController extends Controller {

	var $payment_channel;
	var $trans_id;	
	var $amount;
	var $currency;
	var $shared_key;
	var $mall_id;
	var $table_order;
	var $table_no_order;
	var $table_field_amount;
	var $table_field_customer_name;
	var $table_field_customer_phone;
	var $table_field_customer_email;
	var $table_field_customer_address;
	var $default_payment_channel;	
	var $customer_name;
	var $customer_phone;
	var $customer_email;
	var $customer_address;
	var $payment_available   = array('15','04','02','14');
	var $session_dokularavel = array();
	var $product_name_format;
	var $show_doku_success_page;
	var $show_finish_page;
	var $your_own_finish_page;
	var $redirect_url;
	

	function __construct() {
		$this->default_payment_channel      = config('dokularavel.DEFAULT_PAYMENT_CHANNEL');
		$this->table_order                  = config('dokularavel.TABLE_ORDER');
		$this->table_no_order               = config('dokularavel.TABLE_FIELD_NO_ORDER');
		$this->table_field_amount           = config('dokularavel.TABLE_FIELD_AMOUNT');
		$this->table_field_customer_name    = config('dokularavel.TABLE_FIELD_CUSTOMER_NAME');
		$this->table_field_customer_phone   = config('dokularavel.TABLE_FIELD_CUSTOMER_PHONE');
		$this->table_field_customer_email   = config('dokularavel.TABLE_FIELD_CUSTOMER_EMAIL');
		$this->table_field_customer_address = config('dokularavel.TABLE_FIELD_CUSTOMER_ADDRESS');
		$this->shared_key                   = config('dokularavel.SHARED_KEY');
		$this->mall_id                      = config('dokularavel.MALL_ID');
		$this->currency                     = config('dokularavel.CURRENCY');
		$this->product_name_format  		= config('dokularavel.PRODUCT_NAME_FORMAT');
		$this->show_doku_success_page 		= config('dokularavel.SHOW_DOKU_SUCCESS_PAGE');
		$this->show_finish_page 			= config('dokularavel.SHOW_FINISH_PAGE');
		$this->your_own_finish_page 		= config('dokularavel.YOUR_OWN_FINISH_PAGE');

		if($this->show_finish_page) {
			if($this->your_own_finish_page) {
				$this->redirect_url = $this->your_own_finish_page;
			}else{
				$this->redirect_url = Route('DokuController.finish');
			}
		}

		if(!$this->default_payment_channel || !$this->table_order || !$this->table_no_order || !$this->table_field_amount 
			|| !$this->table_field_customer_name || !$this->table_field_customer_phone || !$this->table_field_customer_email || !$this->table_field_customer_address 
			|| !$this->shared_key || !$this->mall_id || !$this->currency || !$this->product_name_format) {
			die('Please complete the Doku Laravel settings');
		}


		if($this->show_doku_success_page == FALSE && $this->show_finish_page == FALSE) {
			die('Please set the REDIRECT PAGE setting, at least one set to be TRUE');
		}


		if(Request::get('payment_channel')) {			
			$this->payment_channel = Request::get('payment_channel');
		}else{
			$this->payment_channel = $this->default_payment_channel;
		}

		if(!in_array($this->payment_channel, $this->payment_available)) {
			die('Sorry the payment_channel '.$this->payment_channel.' is not available');
		}


		if(Request::get('trans_id')) {
			$query        = DB::table($this->table_order)->where($this->table_no_order,Request::get('trans_id'))->first();			
			if($query) {				
				Session::put('dokularavel',[
					'trans_id'         =>Request::get('trans_id'),
					'payment_channel'  =>$this->payment_channel,
					'amount'           =>preg_replace('/\D/', '', $query->{$this->table_field_amount}),
					'customer_name'    =>preg_replace('/[^a-zA-Z]+/', '', $query->{$this->table_field_customer_name}),
					'customer_phone'   =>str_limit(preg_replace('/\D/', '', $query->{$this->table_field_customer_phone}), 12, ''),
					'customer_email'   =>$query->{$this->table_field_customer_email},
					'customer_address' =>$query->{$this->table_field_customer_address}
					]);
			}else{
				die('the trans_id value is not found');
			}
		}		


		//Init class variable 
		$this->session_dokularavel = Session::get('dokularavel');
		$this->trans_id            = $this->session_dokularavel['trans_id'];
		$this->payment_channel     = $this->session_dokularavel['payment_channel'];
		$this->amount              = $this->session_dokularavel['amount'];
		$this->customer_name       = $this->session_dokularavel['customer_name'];
		$this->customer_phone      = $this->session_dokularavel['customer_phone'];
		$this->customer_address    = $this->session_dokularavel['customer_address'];
		$this->customer_email      = $this->session_dokularavel['customer_email'];
					
	}

	public function checkParams() {
		$payment_available_txt = implode(',',$this->payment_available);
		$validator = Validator::make([
				'trans_id'         =>$this->trans_id,
				'payment_channel'  =>$this->payment_channel,
				'amount'           =>$this->amount,
				'customer_name'    =>$this->customer_name,
				'customer_phone'   =>$this->customer_phone,
				'customer_email'   =>$this->customer_email,
				'customer_address' =>$this->customer_address,
			],[
				'trans_id'         =>'required|string|exists:'.$this->table_order.','.$this->table_no_order,
				'payment_channel'  =>'required|in:'.$payment_available_txt,
				'amount'           =>'required|integer',
				'customer_name'    =>'required|string|min:3',
				'customer_phone'   =>'required|string|max:12',
				'customer_email'   =>'required|email',
				'customer_address' =>'required|string|min:5'
			]);

		if($validator->fails()) {
			$message = json_encode($validator->messages());
			echo $message;
			exit;
		}
	}

	public function index() {				
		//Validation the parameters
		$this->checkParams();				
		
		$this->amount = $this->amount.'.00';				

		$params = array(
			'amount'   => $this->amount,
			'invoice'  => $this->trans_id,
			'currency' => $this->currency
		);				
				
		$data['shared_key']      = $this->shared_key;
		$data['mall_id']         = $this->mall_id;
		$data['words']           = $this->doCreateWords($params);
		$data['amount']          = $this->amount;
		$data['invoice']         = $this->trans_id;
		$data['currency']        = $this->currency;
		$data['payment_channel'] = $this->payment_channel;	

		$hook = new \App\Http\Controllers\DokuHookController;
		$hook->beforePayment($data);		
		
		return view('dokularavel::payment_form',$data);
	}

	public function pay() {		
		$this->checkParams();

		$hook = new \App\Http\Controllers\DokuHookController;

		$token            = Request::get('doku_token');
		$pairing_code     = Request::get('doku_pairing_code');
		$invoice_no       = Request::get('doku_invoice_no');
		$amount           = Request::get('doku_amount');
		$currency         = Request::get('doku_currency');	
		$chain 			  = Request::get('doku_chain_merchant');		
		
		$customer_name    = $this->customer_name;
		$customer_phone   = $this->customer_phone;
		$customer_email   = $this->customer_email;
		$customer_address = $this->customer_address;	

		if(!$token || !$pairing_code || !$invoice_no || !$amount || !$currency || !$chain || !$customer_name || !$customer_phone || !$customer_email || !$customer_address) {
			$param                     = Request::all();
			$param['customer_name']    = $customer_name;
			$param['customer_phone']   = $customer_phone;
			$param['customer_email']   = $customer_email;
			$param['customer_address'] = $customer_address;

			echo json_encode($param);
			exit;
		}

		$params = array(
			'amount'       => $amount,
			'invoice'      => $invoice_no,
			'currency'     => $currency,
			'pairing_code' => $pairing_code,
			'token'        => $token
		);
		
		$words    = $this->doCreateWords($params);
		$wordsRaw = $this->doCreateWordsRaw($params);

		$basket[] = array(
			'name'     => str_replace('[invoice_no]',$invoice_no,$this->product_name_format),
			'amount'   => $amount,
			'quantity' => 1,
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

		$ymdis = date('YmdHis');
		$dataPayment = array(
			'req_mall_id'           => $this->mall_id,
			'req_chain_merchant'    => $chain,
			'req_amount'            => $amount,
			'req_words'             => $words,
			'req_words_raw' 		=> $wordsRaw,
			'req_purchase_amount'   => $amount,
			'req_trans_id_merchant' => $invoice_no,
			'req_request_date_time' => $ymdis,
			'req_currency'          => $currency,
			'req_purchase_currency' => $currency,
			'req_session_id'        => sha1($ymdis),
			'req_name'              => $customer_name,
			'req_payment_channel'   => $this->payment_channel,
			'req_basket'            => $basket,
			'req_mobile_phone'		=> $customer_phone,
			'req_email'             => $customer_email,
			'req_token_id'          => $token, 
			'req_address' 			=> $customer_address			
		);


		if($this->payment_channel == '15') { //If Payment Credit Card		

			$responsePrePayment = $this->doPrePayment($data);			

			if($responsePrePayment->res_response_code == '0000'){
							
				$result = $this->doPayment($dataPayment); 							

				if($result->res_response_code == '0000'){

			        $result->res_redirect_url   = ($this->show_finish_page)?$this->redirect_url:NULL;
			        $result->res_show_doku_page = $this->show_doku_success_page; 	

			        Session::put('dokularavel_finished',$invoice_no);	    

			        
					$hook->afterPayment(true,$dataPayment);    

					echo json_encode($result);
				}else{

					$hook->afterPayment(false,$dataPayment); 

					echo json_encode($result);
				}
			}else{

				$hook->afterPayment(false,$dataPayment); 

				echo json_encode($responsePrePayment);
			}



		}elseif ($this->payment_channel == '04') { //If Payment Doku Wallet
			$ymdis = date('YmdHis');
			
			$result = $this->doPayment($dataPayment); 
			
			Cache::put('doPayment',$invoice_no.':'.json_encode($result), 30);

			if($result->res_response_code == '0000'){

		        $result->res_redirect_url   = ($this->show_finish_page)?$this->redirect_url:NULL;
		        $result->res_show_doku_page = $this->show_doku_success_page;	

		        $hook->afterPayment(true,$dataPayment); 	  

		        Session::put('dokularavel_finished',$invoice_no);      

				echo json_encode($result);
			}else{

				$hook->afterPayment(false,$dataPayment); 

				echo json_encode($result);
			}


		}elseif ($this->payment_channel == '02') { //If payment mandiri clickpay
			$dataPayment['req_card_number']      = str_replace(" - ", "", Request::get('cc_number'));
			$dataPayment['req_challenge_code_1'] = Request::get('CHALLENGE_CODE_1');
		    $dataPayment['req_challenge_code_2'] = Request::get('CHALLENGE_CODE_2');
		    $dataPayment['req_challenge_code_3'] = Request::get('CHALLENGE_CODE_3');
		    $dataPayment['req_response_token']   = Request::get('response_token');

		    $result = $this->doDirectPayment($dataPayment);
		    if($result->res_response_code == '0000'){
		    	
		    	$result->res_redirect_url   = ($this->show_finish_page)?$this->redirect_url:NULL;
		        $result->res_show_doku_page = $this->show_doku_success_page;	

		        $hook->afterPayment(true,$dataPayment); 

		        Session::put('dokularavel_finished',$invoice_no);

			    echo json_encode($result);
			}else{

				$hook->afterPayment(false,$dataPayment); 

			    echo json_encode($result);
			}


		}else{ // If Payment ALFA
			$result = $this->doGeneratePaycode($dataPayment);

			if($result->res_response_code == '0000'){
				
				$result->res_redirect_url   = ($this->show_finish_page)?$this->redirect_url:NULL;
		        $result->res_show_doku_page = $this->show_doku_success_page;	

		        $hook->afterPayment(true,$dataPayment); 

		        Session::put('dokularavel_finished',$invoice_no);

			    echo json_encode($result);
			}else{

				$hook->afterPayment(false,$dataPayment); 

			    echo json_encode($result);
			}
		}
	}

	public function debug() {

		if(config('dokularavel.DEBUG_MODE') == FALSE) abort(404);

		$faker = \Faker\Factory::create('id_ID');

		$invoice_no = 'tandamata_inv_'.time();
		DB::table($this->table_order)->insert([
			$this->table_no_order=>$invoice_no,
			$this->table_field_amount=>rand(100000,900000),
			$this->table_field_customer_name=>$faker->name,
			$this->table_field_customer_address=>$faker->address,
			$this->table_field_customer_phone=>$faker->phoneNumber,
			$this->table_field_customer_email=>$faker->email
			]);

		foreach($this->payment_available as $pa) {
			echo '<a href="'.Route("DokuController.index").'?trans_id='.$invoice_no.'&payment_channel='.$pa.'">'.$invoice_no.' Payment Channel '.$pa.'</a><br/>';
		}		

		echo '<hr/>';

		echo '<strong>doPrePaymentRaw</strong><br/>';
		echo Cache::get('doPrePaymentRaw');

		echo '<hr/>';

		echo '<strong>doPaymentRaw</strong><br/>';
		echo Cache::get('doPaymentRaw');

		echo '<hr/>';
		echo '<strong>Doku Laravel Session</strong>';
		dd(Session::all());


	}

	public function finish() {

		if(!$this->session_dokularavel) {
			return redirect()->route('DokuController.index');
		}

		$invoice_no = $this->session_dokularavel['trans_id'];

		if(!$invoice_no) {
			return redirect()->route('DokuController.index');
		}
		
		if(Session::get('dokularavel_finished') != $invoice_no) {
			return redirect()->route('DokuController.index');
		}


		//Destroy Session 
		Session::forget('dokularavel');
		Session::forget('dokularavel_finished');

		$data['doku_amount']  = $this->session_dokularavel['amount'];
		$data['doku_invoice'] = $this->session_dokularavel['trans_id'];
		return view('dokularavel::finish',$data);
	}


	public function checkPaymentStatus($invoice_no) {

		$responseXML = $this->doCheckPaymentStatus($invoice_no);

		echo $responseXML;
	}

	
}