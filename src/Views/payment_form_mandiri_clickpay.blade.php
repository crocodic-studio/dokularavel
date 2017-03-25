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
	            /* hide show payment channel */
	            $('#select-paymentchannel').change(function(){
	                $('.channel').hide();
	                $('#' + $(this).val()).show();
	            });

	            var data = new Object();

	            data.req_cc_field = 'cc_number';
	            data.req_challenge_field = 'CHALLENGE_CODE_1';

	            dokuMandiriInitiate(data);

	        });
    	</script>
    	<script type="text/javascript">
	        jQuery(function($) {
	            $('.cc-number').payment('formatCardNumber');

	            $.fn.toggleInputError = function(erred) {
	                this.parent('.form-group').toggleClass('has-error', erred);
	                return this;
	            };

	            $('#cc_number').change(function() {
	            	$('.cc-number').toggleInputError(!$.payment.validateCardNumber($('.cc-number').val()));
	            })

	            var challenge3 = Math.floor(Math.random() * 999999999);
	            $("#challenge_div_3").text(challenge3);
	            $("#CHALLENGE_CODE_3").val(challenge3);


	        });
    	</script>

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
			        Mandiri Clickpay
			    </div><!-- end select payment channel -->

			    <div class="content-payment-channel padd-default"><!-- start content payment channel -->
			        <form method="post" action="{{ Route('DokuController.pay') }}">
			        <div id="mandiriclickpay" class="channel"> <!-- mandiri clickpay -->
			            <div class="logo-payment-channel right-paychan mandiriclickpay"></div>

			            <div class="styled-input">
			                <input type="text" id="cc_number" name="cc_number" class="cc-number" required style="border:1px solid red" />
			                <label style="color: #d10000">Enter Card Number Here...</label>
			            </div>
			            <div class="desc">
			                Pastikan bahwa kartu Anda telah diaktivasi melalui layanan mandiri Internet Banking Bank Mandiri pada menu Authorized Payment agar dapat melakukan transaksi Internet Payment.
			            </div>
			            <div class="line"></div>
			            <div class="token">
			                <img src="http://staging.doku.com/doku-js/assets/images/token.png" alt="" class="fleft" />
			                <div class="text-token desc fright">
			                    Gunakan token pin mandiri untuk bertransaksi. Nilai yang dimasukkan pada token Anda (Metode APPLI 3)
			                </div>
			                <div class="clear"></div>
			            </div>
			            <div class="list-chacode">
			                <ul>
			                    <li>
			                        <div class="text-chacode">Challenge Code 1</div>
			                        <input type="text" id="CHALLENGE_CODE_1" name="CHALLENGE_CODE_1" placeholder="Please Enter Card Number First" readonly="true" required/>
			                        <div class="clear"></div>
			                    </li>
			                    <li>
			                        <div class="text-chacode">Challenge Code 2</div>
			                        <div class="num-chacode">{{ str_replace('.00','',$amount) }}</div>
			                        <input type="hidden" name="CHALLENGE_CODE_2" value="{{ str_replace('.00','',$amount) }}"/> 
			                        <div class="clear"></div>
			                    </li>
			                    <li>
			                        <div class="text-chacode">Challenge Code 3</div>
			                        <div class="num-chacode" id="challenge_div_3"></div>
			                        <input type="hidden" name="CHALLENGE_CODE_3" id="CHALLENGE_CODE_3" value=""/>
			                        <div class="clear"></div>
			                    </li>
			                    <div class="clear"></div>
			                </ul>
			            </div>
			            <div class="validasi">
			                <div class="styled-input fleft width50">
			                    <input type="text" required name="response_token">
			                    <label>Token Response</label>
			                </div>
			                <div class="clear"></div>
								<span title="Enter token code that generated here" class="tooltip tolltips-wallet">
									   <span title="More"><img src="http://staging.doku.com/doku-js/assets/images/icon-help.png" alt="" style="margin: 0 0 0 10px;" /></span>
								</span>
			            </div>
			            <input type="hidden" name="doku_invoice_no" required value="{{ $invoice }}">
			            <input type="submit" value="Process Payment" class="default-btn">
			        </div><!-- mandiri clickpay -->
			        </form>
			    </div><!-- end content payment channel -->

			</section><!-- end content -->

			<div class="footer">
            	<img src="http://staging.doku.com/doku-js/assets/images/secure.png" alt="">
            	<div class="">Copyright DOKU {{ date('Y') }}</div>
            </div>
			
	</body>
</html>