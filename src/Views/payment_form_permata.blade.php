<!DOCTYPE HTML>
<html>
	<head>
		<title>DOKU Payment</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js"></script>
		<script src="{{$domain}}/doku-js/assets/js/doku.js?version=<?php echo time()?>"></script>
		<link href="{{$domain}}/doku-js/assets/css/doku.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" rel="stylesheet">

		<script type="text/javascript">
			$(function() {
				setInterval(function() {
					$('img').each(function() {
						var src = $(this).attr('src');
						src = src.replace('http://luna2.nsiapay.com','http://staging.doku.com');
						$(this).attr('src',src);
					})
				},2000);				
			})
		</script>
	</head>
	<body>			
			<section class="default-width"><!-- start content -->

			    <div class="head padd-default"><!-- start head -->
			        <div class="left-head fleft">
			            <img src="https://staging.doku.com/doku-js/assets/images/logo-merchant1.png" alt="" />
			        </div>
			        <div class="right-head fright" style="text-align:right">
			            <div class="text-totalpay color-two">ID Invoice : {{$invoice}}, Total Payment ( IDR )</div>
			            <div class="amount color-one">{{ number_format($amount) }}</div>
			        </div>
			        <div class="clear"></div>
			    </div><!-- end head -->

			    <div class="select-payment-channel color-border padd-default"><!-- start select payment channel -->
			        <div style='float:left;width:50%;padding-top:10px'>
			        	Bank Transfer / ATM Bersama
			        </div>
			        <div style="float:right;width:50%;text-align:right">
			        	<img height="40px" src='https://staging.doku.com/merchant_data/ocov2/images/jaringanatm.jpg'/>
			        </div>
			        <div class="clear"></div>
			    </div><!-- end select payment channel -->

			    <div class="content-payment-channel padd-default"><!-- start content payment channel -->
			        
			    	<p>Click the <strong>Get Payment Code button</strong> and note down the code that appears, in order to make payment at the nearest ATM or Internet/Mobile Banking (except for <strong>BCA Internet Banking</strong>)</p>
			    	<br/>
					<p>Please attention : The payment code will expire after a certain period. Your purchase will be cancelled if payment is made after that period.</p>
					<form method='post' action='{{Route("DokuController.pay")}}'>
			        <input class="default-btn font-reg" value="Get Payment Code" id="submitcc" type="submit">
			        </form>
			    </div><!-- end content payment channel -->

			</section><!-- end content -->	
			<div class="footer">
            	<img src="http://staging.doku.com/doku-js/assets/images/secure.png" alt="">
            	<div class="">Copyright DOKU {{ date('Y') }}</div>
            </div>		
	</body>
</html>