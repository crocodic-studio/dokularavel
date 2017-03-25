<?php 
namespace crocodicstudio\dokularavel\Controllers;

use Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class DokuController extends Controller {
			
	var $currency;
	var $shared_key;
	var $mall_id;
	var $table_order;
	var $table_field_no_order;
	var $table_field_amount;
	var $table_field_customer_name;
	var $table_field_customer_phone;
	var $table_field_customer_email;
	var $table_field_customer_address;
	var $table_field_payment_status;
	var $table_field_payment_date;
	var $table_field_payment_channel;
	var $table_field_payment_approval_code;
	var $table_field_payment_session_id;
	var $default_payment_channel;	

	var $payment_available = array();
	var $product_name_format;
	var $show_doku_success_page;
	var $show_finish_page;
	var $your_own_finish_page;
	var $redirect_url;
	var $invoice;
	

	function __construct() {
		$this->default_payment_channel      = config('dokularavel.DEFAULT_PAYMENT_CHANNEL');
		$this->table_order                  = config('dokularavel.TABLE_ORDER');
		$this->table_field_no_order         = config('dokularavel.TABLE_FIELD_NO_ORDER');
		$this->table_field_amount           = config('dokularavel.TABLE_FIELD_AMOUNT');
		$this->table_field_customer_name    = config('dokularavel.TABLE_FIELD_CUSTOMER_NAME');
		$this->table_field_customer_phone   = config('dokularavel.TABLE_FIELD_CUSTOMER_PHONE');
		$this->table_field_customer_email   = config('dokularavel.TABLE_FIELD_CUSTOMER_EMAIL');
		$this->table_field_customer_address = config('dokularavel.TABLE_FIELD_CUSTOMER_ADDRESS');
		$this->table_field_payment_date 	= config('dokularavel.TABLE_FIELD_PAYMENT_DATE');
		$this->table_field_payment_status 	= config('dokularavel.TABLE_FIELD_PAYMENT_STATUS'); 
		$this->table_field_payment_channel 	= config('dokularavel.TABLE_FIELD_PAYMENT_CHANNEL');		
		$this->table_field_payment_approval_code = config('dokularavel.TABLE_FIELD_PAYMENT_APPROVAL_CODE');
		$this->table_field_payment_session_id = config('dokularavel.TABLE_FIELD_PAYMENT_SESSION_ID');
		$this->shared_key                   = config('dokularavel.SHARED_KEY');
		$this->mall_id                      = config('dokularavel.MALL_ID');
		$this->currency                     = config('dokularavel.CURRENCY');
		$this->product_name_format  		= config('dokularavel.PRODUCT_NAME_FORMAT');
		$this->show_doku_success_page 		= config('dokularavel.SHOW_DOKU_SUCCESS_PAGE');
		$this->show_finish_page 			= config('dokularavel.SHOW_FINISH_PAGE');
		$this->your_own_finish_page 		= config('dokularavel.YOUR_OWN_FINISH_PAGE');
		$this->payment_available 			= config('dokularavel.AVAILABLE_PAYMENT_CHANNEL');

		if($this->show_finish_page) {
			if($this->your_own_finish_page) {
				$this->redirect_url = $this->your_own_finish_page;
			}else{
				$this->redirect_url = Route('DokuController.finish');
			}
		}

		if(!$this->default_payment_channel || !$this->table_order || !$this->table_field_no_order || !$this->table_field_amount 
			|| !$this->table_field_customer_name || !$this->table_field_customer_phone || !$this->table_field_customer_email || !$this->table_field_customer_address 
			|| !$this->shared_key || !$this->mall_id || !$this->currency || !$this->product_name_format) {
			die('Please complete the Doku Laravel settings');
		}


		if($this->show_doku_success_page == FALSE && $this->show_finish_page == FALSE) {
			die('Please set the REDIRECT PAGE setting, at least one set to be TRUE');
		}
		

		$trans_id        = (Session::get('dokularavel_trans_id'))?:$this->default_payment_channel;
		$trans_id        = (Request::get('trans_id'))?:$trans_id;
		$trans_id        = (Request::get('doku_invoice_no'))?:$trans_id;
		$payment_channel = Request::get('payment_channel');

		if($trans_id) {

			Session::put('dokularavel_trans_id',$trans_id);
			
			$query        = DB::table($this->table_order)->where($this->table_field_no_order,$trans_id)->first();	

			if($payment_channel) {
				if($query->{$this->table_field_payment_channel} != $payment_channel) {
					DB::table($this->table_order)
					->where($this->table_field_no_order,$trans_id)
					->update([$this->table_field_payment_channel=>$payment_channel]);
					$query = DB::table($this->table_order)->where($this->table_field_no_order,$trans_id)->first();
				}
			}
					
			if($query) {				
				$this->invoice = [
					'trans_id'         =>$query->{$this->table_field_no_order},
					'payment_channel'  =>$query->{$this->table_field_payment_channel},
					'amount'           =>preg_replace('/\D/', '', $query->{$this->table_field_amount}).'.00',
					'customer_name'    =>preg_replace('/[^a-zA-Z ]+/', '', $query->{$this->table_field_customer_name}),
					'customer_phone'   =>str_limit(preg_replace('/\D/', '', $query->{$this->table_field_customer_phone}), 12, ''),
					'customer_email'   =>$query->{$this->table_field_customer_email},
					'customer_address' =>str_limit($query->{$this->table_field_customer_address},100),
					'payment_status'   =>$query->{$this->table_field_payment_status}
					];		
						
			}else{
				// die('the trans_id value is not found');
			}
		}		
					
	}

	public function checkParams() {
		$payment_available_txt = implode(',',$this->payment_available);
		$validator = Validator::make($this->invoice,[
				'trans_id'         =>'required|string|exists:'.$this->table_order.','.$this->table_field_no_order,
				'payment_channel'  =>'required|in:'.$payment_available_txt,
				'amount'           =>'required|numeric',
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
		if(Request::get('trans_id')=='') die('Transaction aborted because trans_id is not found !');		

		if($this->invoice['payment_status'] == 'PAID') return redirect($this->redirect_url.'?status=success');

		$params = array(
			'amount'   => $this->invoice['amount'],
			'invoice'  => $this->invoice['trans_id'],
			'currency' => $this->currency
		);				
				
		$data['shared_key']      = $this->shared_key;
		$data['mall_id']         = $this->mall_id;
		$data['words']           = $this->doCreateWords($params);
		$data['amount']          = $this->invoice['amount'];
		$data['invoice']         = $this->invoice['trans_id'];
		$data['currency']        = $this->currency;
		$data['payment_channel'] = $this->invoice['payment_channel'];	
		$data['payment_available'] = $this->payment_available;

		if(config('dokularavel.LIVE_MODE') == TRUE) {
			$data['domain'] = 'https://pay.doku.com';
		}else{
			$data['domain' ] = 'http://staging.doku.com';
		}

		$hook = new \App\Http\Controllers\DokuLaravelHookController;
		$hook->beforePayment($data);		

		if(!Request::get('payment_channel')) {
			return view('dokularavel::payment_channel',$data);
		}
		
		if($this->invoice['payment_channel'] == '02') {
			return view('dokularavel::payment_form_mandiri_clickpay',$data);
		}elseif ($this->invoice['payment_channel'] == '05') {
			return view('dokularavel::payment_form_permata',$data);
		}else{
			return view('dokularavel::payment_form',$data);
		}		
	}

	public function pay() {		
		$this->checkParams();

		$hook = new \App\Http\Controllers\DokuLaravelHookController;

		$token            = Request::get('doku_token');
		$pairing_code     = Request::get('doku_pairing_code');
		$invoice_no       = Request::get('doku_invoice_no')?:$this->invoice['trans_id'];
		$amount           = Request::get('doku_amount')?:$this->invoice['amount'];
		$currency         = Request::get('doku_currency')?:$this->currency;	
		$chain 			  = Request::get('doku_chain_merchant')?:'NA';		
		

		$params = array(
			'amount'       => $amount,
			'invoice'      => $invoice_no,
			'currency'     => $currency
		);

		if($pairing_code) {
			$params['pairing_code'] = $pairing_code;
		}

		if($token) {
			$params['token'] = $token;
		}
		
		$words    = $this->doCreateWords($params);
		$wordsRaw = $this->doCreateWordsRaw($params);

		$basket[] = array(
			'name'     => str_replace('[invoice_no]',$invoice_no,$this->product_name_format),
			'amount'   => $amount,
			'quantity' => 1,
			'subtotal' => $amount
		);

		$customer = array(
			'name'         => trim($this->invoice['customer_name']),
			'data_phone'   => trim($this->invoice['customer_phone']),
			'data_email'   => trim($this->invoice['customer_email']),
			'data_address' => str_limit(trim($this->invoice['customer_address']),100)
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
			'req_name'              => trim($this->invoice['customer_name']),
			'req_payment_channel'   => $this->invoice['payment_channel'],
			'req_basket'            => $basket,
			'req_mobile_phone'		=> trim($this->invoice['customer_phone']),
			'req_email'             => trim($this->invoice['customer_email']),
			'req_token_id'          => $token, 
			'req_address' 			=> str_limit(trim($this->invoice['customer_address']),100)			
		);

		Cache::forever('dataPayment',$dataPayment);

		


		if($this->invoice['payment_channel'] == '15') { //If Payment Credit Card		

			$data = array(
				'req_token_id'     => $token,
				'req_pairing_code' => $pairing_code,
				'req_customer'     => $customer,
				'req_basket'       => $basket,
				'req_words'        => $words
			);

			$responsePrePayment = $this->doPrePayment($data);			

			if($responsePrePayment->res_response_code == '0000'){
							
				$result = $this->doPayment($dataPayment); 							

				if($result->res_response_code == '0000'){

			        $result->res_redirect_url   = ($this->show_finish_page)?$this->redirect_url:NULL;
			        $result->res_show_doku_page = $this->show_doku_success_page; 	

			        Session::put('dokularavel_finished',$invoice_no);	    

			        DB::table($this->table_order)
					->where($this->table_field_no_order,$this->invoice['trans_id'])
					->update([
						$this->table_field_payment_status        =>'PAID',
						$this->table_field_payment_date          =>date('Y-m-d H:i:s'),
						$this->table_field_payment_approval_code =>$result->res_approval_code,
						$this->table_field_payment_session_id => $dataPayment['req_session_id']
						]);
			        
					$hook->afterPayment(true,$dataPayment);    					

					echo json_encode($result);
				}else{

					DB::table($this->table_order)
					->where($this->table_field_no_order,$this->invoice['trans_id'])
					->update([
						$this->table_field_payment_status        =>'UNPAID',
						$this->table_field_payment_session_id => $dataPayment['req_session_id']						
						]);

					$hook->afterPayment(false,$dataPayment); 

					echo json_encode($result);
				}
			}else{

				$hook->afterPayment(false,$dataPayment); 

				echo json_encode($responsePrePayment);
			}



		}elseif ($this->invoice['payment_channel'] == '04') { //If Payment Doku Wallet
			$ymdis = date('YmdHis');
			
			$result = $this->doPayment($dataPayment); 
			
			Cache::forever('doPayment',$invoice_no.':'.json_encode($result));

			if($result->res_response_code == '0000'){

		        $result->res_redirect_url   = ($this->show_finish_page)?$this->redirect_url:NULL;
		        $result->res_show_doku_page = $this->show_doku_success_page;	

		        DB::table($this->table_order)
					->where($this->table_field_no_order,$this->invoice['trans_id'])
					->update([
						$this->table_field_payment_status        =>'PAID',
						$this->table_field_payment_date          =>date('Y-m-d H:i:s'),
						$this->table_field_payment_approval_code =>$result->res_approval_code,
						$this->table_field_payment_session_id => $dataPayment['req_session_id']
						]);

		        $hook->afterPayment(true,$dataPayment); 	  

		        Session::put('dokularavel_finished',$invoice_no);      

				echo json_encode($result);
			}else{

				DB::table($this->table_order)
					->where($this->table_field_no_order,$this->invoice['trans_id'])
					->update([
						$this->table_field_payment_status        =>'UNPAID',
						$this->table_field_payment_session_id => $dataPayment['req_session_id']						
						]);

				$hook->afterPayment(false,$dataPayment); 

				echo json_encode($result);
			}


		}elseif ($this->invoice['payment_channel'] == '02') { //If payment mandiri clickpay			

			$dataPayment['req_card_number']      = str_replace(" - ", "", Request::get('cc_number'));
			$dataPayment['req_challenge_code_1'] = Request::get('CHALLENGE_CODE_1');
		    $dataPayment['req_challenge_code_2'] = Request::get('CHALLENGE_CODE_2');
		    $dataPayment['req_challenge_code_3'] = Request::get('CHALLENGE_CODE_3');
		    $dataPayment['req_response_token']   = Request::get('response_token');		

		    unset($dataPayment['req_token_id']);    	   

		    $result = $this->doDirectPayment($dataPayment);
		    if($result->res_response_code == '0000'){
		    	
		    	$result->res_redirect_url   = ($this->show_finish_page)?$this->redirect_url:NULL;
		        $result->res_show_doku_page = $this->show_doku_success_page;	

		        DB::table($this->table_order)
					->where($this->table_field_no_order,$this->invoice['trans_id'])
					->update([
						$this->table_field_payment_status        =>'PAID',
						$this->table_field_payment_date          =>date('Y-m-d H:i:s'),
						$this->table_field_payment_approval_code =>$result->res_approval_code,
						$this->table_field_payment_session_id => $dataPayment['req_session_id']	
						]);		        

		        $hook->afterPayment(true,$dataPayment); 

		        Session::put('dokularavel_finished',$invoice_no);

			    return redirect($this->redirect_url.'?status=success');
			}else{

				DB::table($this->table_order)
					->where($this->table_field_no_order,$this->invoice['trans_id'])
					->update([
						$this->table_field_payment_status        =>'UNPAID',
						$this->table_field_payment_session_id => $dataPayment['req_session_id']							
						]);

				$hook->afterPayment(false,$dataPayment); 

			    return redirect($this->redirect_url.'?status=failed');			    
			}


		}elseif ($this->invoice['payment_channel'] == '05') { //Payment channel Permata VA ATM Bersama
			unset($dataPayment['req_words_raw']);
			unset($dataPayment['req_currency']);
			unset($dataPayment['req_purchase_currency']);
			unset($dataPayment['req_payment_channel']);
			unset($dataPayment['req_basket']);
			unset($dataPayment['req_token_id']);
			unset($dataPayment['req_address']);
			unset($dataPayment['req_mobile_phone']);

			$result = $this->doGeneratePaycode($dataPayment);

			if($result->res_response_code == '0000'){	

				$payment_code = $result->res_pay_code;

				$permata_code = config('dokularavel.PERMATA_CODE');
				$payment_code = ($permata_code)?$permata_code.$payment_code:$payment_code;

				DB::table($this->table_order)
					->where($this->table_field_no_order,$this->invoice['trans_id'])
					->update([
						// $this->table_field_payment_status        =>'WAITING TRANSFER',
						$this->table_field_payment_session_id => $dataPayment['req_session_id']							
						]);

		        // $hook->afterPayment(true,$dataPayment); 

		        // return redirect($this->redirect_url.'?status=atmWaiting&payment_code='.$payment_code);	

					return redirect(Route('DokuController.paycode').'?paycode='.$payment_code);
			}else{

				DB::table($this->table_order)
					->where($this->table_field_no_order,$this->invoice['trans_id'])
					->update([
						$this->table_field_payment_status        =>'FAILED',
						$this->table_field_payment_session_id => $dataPayment['req_session_id']							
						]);

				$hook->afterPayment(false,$dataPayment); 

			    return redirect($this->redirect_url.'?status=failed');	
			}
		}
	}

	public function notify($screet_code) {

		if(!$screet_code) abort(404);

		if($screet_code != config('dokularavel.NOTIFY_SCREET_CODE')) abort(404);

		$allldata 		   = Request::all();
		$trans_id          = urldecode(Request::get('TRANSIDMERCHANT'));
		$status            = Request::get('RESULTMSG');	
		$payment_date_time = Request::get('PAYMENTDATETIME');	
		$approvalcode      = Request::get('APPROVALCODE');
			
		if($trans_id) {
			if($status == 'SUCCESS' && $approvalcode) {				
				try{
					$query = DB::table($this->table_order)
					->where($this->table_field_no_order,$trans_id)
					->update([
						$this->table_field_payment_status=>'PAID',
						$this->table_field_payment_date=>$payment_date_time,
						$this->table_field_payment_approval_code=>$approvalcode
						]);

					$hook = new \App\Http\Controllers\DokuLaravelHookController;	
					$hook->afterPayment(true,$allldata);
				}catch(\Exception $e) {
					die('Stop2');
				}
			}else{
				$hook->afterPayment(false,$allldata);
				try{	
					$query = DB::table($this->table_order)
					->where($this->table_field_no_order,$trans_id)
					->update([
						$this->table_field_payment_status=>'FAILED'
						]);
				}catch(\Exception $e) {
					die('Stop3');
				}
			}
			echo 'Continue';
		}else{
			echo 'Stop1'; 
		}		
	}

	public function checkStatus() {
		$trans_id = Request::get('trans_id');
		if(!$trans_id) {
			return redirect($this->redirect_url.'?status=failed');
		}else{
			$check = DB::table($this->table_order)
			->where($this->table_field_no_order,$trans_id)
			->where($this->table_field_payment_status,'PAID')->first();
			if(!$check) {

				// DB::table($this->table_order)->where($this->table_field_no_order,$trans_id)->where($this->table_field_payment_status,'WAITING TRANSFER')->update([
				// 		$this->table_field_payment_status=>'IN PROCESS'
				// 	]);

				return redirect(Route('DokuController.waitingTransfer'));	
			}else{
				return redirect($this->redirect_url.'?status=success');
			}
		}
	}

	public function paycode() {
		if(!Session::get('dokularavel_trans_id')) return redirect(route('DokuController.index').'?r=invoice_null');

		$invoice_no = $this->invoice['trans_id'];
		$row = DB::table($this->table_order)->where($this->table_field_no_order,$invoice_no)->first();

		if(!$invoice_no) {
			return redirect(route('DokuController.index').'?r=invoice_null');
		}
		

		$data['doku_amount']       = $this->invoice['amount'];
		$data['doku_invoice']      = $invoice_no;
		$data['doku_payment_code'] = Request::get('paycode');
		return view('dokularavel::atmbersama_paycode',$data);
	}

	public function waitingTransfer() {
		if(!Session::get('dokularavel_trans_id')) return redirect(route('DokuController.index').'?r=invoice_null');

		$invoice_no = $this->invoice['trans_id'];
		$row = DB::table($this->table_order)->where($this->table_field_no_order,$invoice_no)->first();

		if(!$invoice_no) {
			return redirect(route('DokuController.index').'?r=invoice_null');
		}
		

		$data['doku_amount']       = $this->invoice['amount'];
		$data['doku_invoice']      = $invoice_no;
		$data['doku_payment_code'] = Request::get('paycode');
		return view('dokularavel::atmbersama_waiting',$data);
	}

	public function finish() {

		if(!Session::get('dokularavel_trans_id')) return redirect(route('DokuController.index').'?r=invoice_null');

		$invoice_no = $this->invoice['trans_id'];
		$row = DB::table($this->table_order)->where($this->table_field_no_order,$invoice_no)->first();

		if(!$invoice_no) {
			return redirect(route('DokuController.index').'?r=invoice_null');
		}
		

		$data['doku_amount']       = $this->invoice['amount'];
		$data['doku_invoice']      = $invoice_no;
		$data['doku_payment_code'] = Request::get('payment_code');
		return view('dokularavel::finish',$data);
	}


	public function checkPaymentStatus($invoice_no) {

		$responseXML = $this->doCheckPaymentStatus($invoice_no);

		echo $responseXML;
	}

	public function debug() {

		if(config('dokularavel.DEBUG_MODE') == FALSE) abort(404);

		// $faker = \Faker\Factory::create('id_ID');

		// $invoice_no = 'tandamata_inv_'.time();
		// DB::table($this->table_order)->insert([
		// 	$this->table_field_no_order=>$invoice_no,
		// 	$this->table_field_amount=>rand(100000,900000),
		// 	$this->table_field_customer_name=>$faker->name,
		// 	$this->table_field_customer_address=>$faker->address,
		// 	$this->table_field_customer_phone=>$faker->phoneNumber,
		// 	$this->table_field_customer_email=>$faker->email
		// 	]);

		// foreach($this->payment_available as $pa) {
		// 	echo '<a target="doku" href="'.Route("DokuController.index").'?trans_id='.$invoice_no.'&payment_channel='.$pa.'">'.$invoice_no.' Payment Channel '.$pa.'</a><br/>';
		// }		

		echo '<hr/>';

		echo '<strong>doPrePaymentRaw</strong><br/>';
		echo Cache::get('doPrePaymentRaw');

		echo '<hr/>';

		echo '<strong>doPaymentRaw</strong><br/>';
		echo Cache::get('doPaymentRaw');

		echo '<hr/>';

		echo '<strong>doDirectPayment</strong><br/>';
		echo Cache::get('doDirectPaymentRaw');

		echo '<hr/>';

		echo '<strong>dataPayment</strong><br/>';
		echo '<pre>';
		echo print_r(Cache::get('dataPayment'));
		echo '</pre>';

		echo '<hr/>';

		echo '<strong>doGeneratePaycode</strong><br/>';
		echo '<pre>';
		echo print_r(Cache::get('doGeneratePaycodeRaw'));
		echo '</pre>';

		echo '<hr/>';
		echo '<strong>Doku Laravel Session</strong>';
		dd(Session::all());


	}

	
}