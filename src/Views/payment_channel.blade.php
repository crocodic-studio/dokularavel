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
		<script src="http://staging.doku.com/doku-js/assets/js/doku.js?version=<?php echo time()?>"></script>
		<link href="http://staging.doku.com/doku-js/assets/css/doku.css" rel="stylesheet">
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
				$('.payment_channel').click(function() {
					$(this).parent().parent().find('input:radio').prop('checked',false);					
					$(this).find('input:radio').prop('checked',true);
				})			
			})
		</script>
		<style type="text/css">
			.payment_channel {
				border:1px solid #dddddd;
				padding:20px;
				margin-bottom: 5px;			    
			    cursor: pointer;
			    display: block;
			    text-decoration: none;
			    background: #efefef;
			}
			.payment_channel:hover {
				background: #fefefe;
			}
			.payment_channel img {
				float: right;
			}
		</style>		
	</head>
	<body>			
			<section class="default-width"><!-- start content -->

			    <div class="head padd-default"><!-- start head -->
			        <div class="left-head fleft">
			            <img src="https://staging.doku.com/doku-js/assets/images/logo-merchant1.png" alt="" />
			        </div>
			        <div class="right-head fright" style="text-align:right">
			            <div class="text-totalpay color-two">{{$invoice}}</div>
			            <div class="amount color-one">{{ number_format($amount) }}</div>
			        </div>
			        <div class="clear"></div>
			    </div><!-- end head -->

			    <div class="select-payment-channel color-border padd-default"><!-- start select payment channel -->
			        Payment Channel
			    </div><!-- end select payment channel -->

			    <div class="content-payment-channel padd-default"><!-- start content payment channel -->
			        <form method="get" action="">
			        	<input type='hidden' name='trans_id' value="{{Request::get('trans_id')}}"/>
			        	<?php 
			        		$payment_channels = array(
			        				['label'=>'Credit Card','id'=>'15','icon'=>asset("public/vendor/dokularavel/logocc.gif")],
			        				['label'=>'Mandiri Clickpay','id'=>'02','icon'=>asset("public/vendor/dokularavel/logo_mandiriclickpay.png")],
			        				['label'=>'Doku Wallet','id'=>'04','icon'=>asset("public/vendor/dokularavel/logo_dokuwallet.png")],
			        				['label'=>'Bank Transfer','id'=>'05','icon'=>asset("public/vendor/dokularavel/logo_atmbersama.jpg")]
			        			);

			        		foreach($payment_channels as $k=>$payment_channel):
			        			
			        			$payment_true = 0;
			        			foreach($payment_available as $p) {
			        				if($payment_channel['id'] == $p) {
			        					$payment_true = 1; 
			        				}
			        			}

			        			if($payment_true == 0) continue;

			        			$checked = (config('dokularavel.DEFAULT_PAYMENT_CHANNEL') == $payment_channel['id'])?"checked":"";
			        	?>
					        				        		
					        		<a href='{{url(config("dokularavel.PAYMENT_PATH"))."?trans_id=".Request::get("trans_id")."&payment_channel=".$payment_channel["id"]}}' class='payment_channel'> {{$payment_channel['label']}} 
					        			<span class='pull-right'><img style="margin-top: -2px" src='{{asset("public/vendor/dokularavel/ic_arrow_right.png")}}'/></span>
					        		</a>
					        	
			        	<?php endforeach;?>			          			       
			        </form>
			    </div><!-- end content payment channel -->

			</section><!-- end content -->

			<div class="footer">
            	<img src="http://staging.doku.com/doku-js/assets/images/secure.png" alt="">
            	<div class="">Copyright DOKU {{ date('Y') }}</div>
            </div>
			
	</body>
</html>