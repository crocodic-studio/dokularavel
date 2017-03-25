<!DOCTYPE HTML>
<html>
	<head>
		<title>DOKU Payment Waiting</title>
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

 						<ul>
		                    <li>
		                    	<div class='form-group' align="center">
		                    		<h3 style="color:#B7042C;">	                    			
		                    			<span id='status' style="font-size:27px">Payment In Process...</span>
		                    		</h3>
		                    		<br/><p>If you have make a payment, please waiting for 4 ~ 6 minutes while Bank transfer process. We will notify to you if your payment has been received.</p>
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
   

                    </div>
                    </div>
                    </section>
                    <div class="footer">
                    <img src="http://staging.doku.com/doku-js/assets/images/secure.png" alt="">
                    <div class="">Copyright DOKU {{date('Y')}}</div>
                    </div>
			                    
			</div>
	</body>
</html>