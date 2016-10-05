<!DOCTYPE HTML>
<html>
	<head>
		<title>DOKU Payment Finish</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
		<link href="http://staging.doku.com/doku-js/assets/css/doku.css" rel="stylesheet">
		<style>
		.paymentcodetitle {
			font-weight: bold;
			color: #0071aa;
			margin:10px 0px 10px 0px;
		}
		</style>
	</head>
	<body>			
			<div doku-div="form-payment"><section class="default-width">
                    <div class="head padd-default">
                    <div class="left-head fleft">
                    <img src="http://staging.doku.com/doku-js/assets/images/logo-merchant1.png" alt="">
                    </div>
                    <div class="right-head fright">
                    <div class="text-totalpay color-two">Total Payment ( IDR ) {{ $doku_invoice }}</div>
                    <div style="text-align:right" class="amount color-one">RP. {{ number_format($doku_amount) }}</div>
                    </div>
                    <div class="clear"></div>
                    </div>                                        
                    
                    <div class="content-payment-channel padd-default">
                    <div id="creditcard" class="channel">
                    <!-- <div class="logo-payment-channel right-paychan cc"></div> -->                    
                    @if(Request::get('status')=='failed')
                    <ul>
	                    <li>
	                    	<div class='form-group'>
	                    		<h3 style="color:#B7042C;" align="center">
	                    			<div id='icon-tick' align="center"><img style="width:30%" src='{{ asset("public/vendor/dokularavel/stop.png") }}'/></div><br/>
	                    			<span style="font-size:27px">Transaction Failed</span>
	                    		</h3>
	                    	</div>
	                    </li>
                    </ul>
                    @elseif(Request::get('status') == 'waiting_payment')
                    <ul>
	                    <li>
	                    	<div class='form-group' align="center">
	                    		<h3 style="color:#B7042C;">	                    			
	                    			<span id='status' style="font-size:27px">Payment In Process...</span>
	                    		</h3>
	                    		<p>If you have make a payment, please waiting for 4 ~ 6 minutes while Bank transfer process. We will notify to you if your payment has been received.</p>
	                    		<form name="formRedirect" id="formRedirect" method="POST" action="{{Route('DokuController.checkStatus')}}">								   
								<input type="hidden" name="trans_id" value="{{$doku_invoice}}">                                								
								<input style="display:none" type="submit" class="default-btn font-reg" value="Check Status">
								</form>
	                    	</div>
	                    </li>
                    </ul>
                    <script type="text/javascript">
                    $(function() {
                    	setInterval(function() {
                    		$('#formRedirect').submit();
                    	},10000);
                    })                    	
                    </script>
                    @elseif(Request::get('status') =='atmWaiting')
 						<div class="color-border padd-default">
							<div class="paymentcode success" style="text-align:center">
								<div class="textva" style="text-align:center">Payment Code</div>
								<div class="numva">{{$doku_payment_code}}</div>
								<div class="clear"></div>
							</div>			
							<br><br>
							<div class="detail-result">
								<ul>
									<li>
										<div class="fleft">Amount</div>
										<div class="fright color-one">IDR&nbsp; {{$doku_amount}}</div>
										<div class="clear"></div>
									</li>
									<li>
										<div class="fleft">Invoice Number</div>
										<div class="fright">{{$doku_invoice}}</div>
										<div class="clear"></div>
									</li>

									<li class='color-one' style="text-align:justify"><strong>NOTE :</strong> Please <strong>do not close this page</strong> until your payment is completed. Please make payment orders as soon as possible. Otherwise your order will be canceled.</li>
									
									<li>
										<div class="paymentcodetitle">&raquo; How to Pay at the ATM</div>
										<div class="clear list-number">
											<ol>
												<li>1. Enter PIN</li>
												<li>2. Choose 'Transfer'. If using ATM BCA, choose 'Others' then 'Transfer'</li>
												<li>3. Choose 'Other Bank Account'</li>
												<li>4. Enter the bank code (Permata is 013) followed by the 16 digit payment code <span class="color-one">{{$doku_payment_code}}</span> as the destination account, then choose 'Correct'</li>
												<li>5. Enter the exact amount as your transaction value. Incorrect transfer amount will result in failed payment</li>
												<li>6. Confirm that the bank code, payment code, and transaction amount is correct, then choose 'Correct'</li>
												<li>7. Done</li>
											</ol>
										</div>
										<br>
										<div class="paymentcodetitle">&raquo; How to Pay Using Internet Banking</div>										
										<div class="color-one" style="margin:5px 0px 5px 0px"><b>Note: </b> Payment cannot be done using BCA Internet Banking</div>
										<div class="clear list-number">
											<ol>
												<li>1. Login to your internet banking account</li>
												<li>2. Choose the bank code (Permata is 013) of your selected virtual account bank</li>
												<li>3. Enter the exact amount as your transaction value</li>
												<li>4. Enter the destination amount using your 16 digit payment code <span class="color-one">{{$doku_payment_code}}</span>  </li>
												<li>5. Confirm that the bank code, payment code, and transaction amount is correct, then choose 'Correct'</li>
												<li>6. Done</li>
											</ol>
										</div>
									</li>
								</ul>
								<p>Please note maybe the system is need to propagation the data from Bank, so after make a payment please waiting about 5 ~ 6 minutes before you press the Confirm Payment button</p>
								<form name="formRedirect" id="formRedirect" method="POST" action="{{Route('DokuController.checkStatus')}}">
								   
								<input type="hidden" name="trans_id" value="{{$doku_invoice}}">                                								
								<input type="submit" class="default-btn font-reg" value="Confirm The Payment">
								</form>
							</div>
						    
						</div>

                    @else
                    <ul>
	                    <li>
	                    	<div class='form-group'>
	                    		<h3 style="color:#00af11;" align="center">
	                    			<div id='icon-tick' align="center"><img style="width:50%" src='{{ asset("public/vendor/dokularavel/icon-tick.png") }}'/></div>
	                    			<span style="font-size:27px">Transaction Success</span>
	                    		</h3>
	                    	</div>
	                    </li>
                    </ul>
                    @endif

                    <br/>
                    <p><a class="default-btn font-reg" href='{{url("/")}}' title='back to homepage'>Back To Homepage</a></p>

                    </div>
                    </div>
                    </section>
                    <div class="footer">
                    <img src="http://staging.doku.com/doku-js/assets/images/secure.png" alt="">
                    <div class="">Copyright DOKU 2016</div>
                    </div>
			                    
			</div>
	</body>
</html>