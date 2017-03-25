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
			var data                 = new Object();
			data.req_merchant_code   = '<?php echo $mall_id?>';
			data.req_chain_merchant  = 'NA';
			data.req_payment_channel = '{{ $payment_channel }}';
			data.req_server_url 	 = '<?php echo route("DokuController.pay")?>';
			data.req_transaction_id  = '<?php echo $invoice ?>';
			data.req_currency        = '<?php echo $currency ?>';
			data.req_amount          = '<?php echo $amount ?>';
			data.req_words           = '<?php echo $words ?>';
			data.req_session_id      = new Date().getTime();
			data.req_form_type       = 'full';
			getForm(data);
		});

		</script>
		<script type="text/javascript">
			$(function() {
				setInterval(function() {
					$('img').each(function() {
						var src = $(this).attr('src');
						src = src.replace('http://luna2.nsiapay.com','{{$domain}}');
						$(this).attr('src',src);
					})
				},2000);				
			})
		</script>

		<style type="text/css">
			.fancybox-outer {
				overflow:auto;
			     -webkit-overflow-scrolling:touch;
			     width: 100%;
			     height: 100%;
			}
		</style>
	</head>
	<body>			
			<div doku-div='form-payment'></div>
	</body>
</html>