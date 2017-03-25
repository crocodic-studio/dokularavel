# DokuLaravel - DOKU Payment Gateway Library For Laravel
[![Total Downloads](https://poser.pugx.org/crocodicstudio/dokularavel/downloads)](https://packagist.org/packages/crocodicstudio/dokularavel) [![Latest Unstable Version](https://poser.pugx.org/crocodicstudio/dokularavel/v/unstable)](https://packagist.org/packages/crocodicstudio/dokularavel) [![License](https://poser.pugx.org/crocodicstudio/dokularavel/license)](https://packagist.org/packages/crocodicstudio/dokularavel) [![Monthly Downloads](https://poser.pugx.org/crocodicstudio/dokularavel/d/monthly)](https://packagist.org/packages/crocodicstudio/dokularavel)

![Doku Laravel](http://crudbooster.com/dokularavel_screenshot.png)

Ini adalah DOKU Payment Gateway Library yang sudah di *compile* menjadi *Package* untuk Laravel. API DOKU ini merupakan API dari DOKU Resmi yang berjenis Merchant Hosted, artinya tetap menggunakan FORM Pembayaran yang digenerate dari DOKU melalui Javascript, dan bukan yang model redirect ke halaman DOKU melainkan Form DOKU ada di server kita dan pembayaran seolah dilakukan di server kita.

## Kebutuhan Dasar
1. **SHARED_KEY** , didapat dari DOKU, silahkan hubungi [DOKU](http://doku.com)
2. **MALL_ID**, didapat dari DOKU, silahkan hubungi [DOKU](http://doku.com)
3. **PERMATA_CODE**, didapat dari DOKU, kode ini dibutuhkan jika opsi pembayaran Permata Bank diaktifkan
4. **TABLE_ORDER**, table yang berkaitan Order/Invoice dan memilik field minimal **no_invoice,customer_name,customer_phone,customer_email,customer_address,total,payment_status,payment_date,payment_channel,payment_approval_code,payment_session_id**

## 1. Instalasi untuk Laravel 5.x
```
composer require crocodicstudio/dokularavel
```
## 2. Tambahkan ke Service Provider (config/app.php)
```
crocodicstudio\dokularavel\DokuLaravelServiceProvider::class,
```
## 3. Publikasi File Konfigurasi
```
php artisan vendor:publish --provider="crocodicstudio\dokularavel\DokuLaravelServiceProvider"
```
## 4. Pengaturan Konfigurasi Dasar
Pada folder config/ akan terdapat file baru bernama **dokularavel.php** yang isinya adalah :  
**Yang wajib diisi atau disesuaikan yakni SHARED_KEY,MALL_ID, dan pengaturan TABLE**
```
return [
	/*
	| ---------------------------------------------------------
	| Setting the payment mode is Sandbox Mode or Live Mode
	| ---------------------------------------------------------
	| if set false it means sandbox mode, else it means live mode 
	| ** PLEASE BE CAREFULL ABOUT CHANGE THE LIVE MODE
	|
	*/
	'LIVE_MODE' => FALSE,
	
	/*
	| ---------------------------------------------------------
	| Setting the payment route in PAYMENT_PATH, SHARED_KEY & MALL_ID is code that you get from DOKU Merchant Page.
	| ---------------------------------------------------------
	|
	*/
	'PAYMENT_PATH' => 'dokularavel',
	'SHARED_KEY'   => NULL, 
	'MALL_ID'      => NULL,
	'PERMATA_CODE' => NULL,
	'CURRENCY'     => 360,
	'NOTIFY_SCREET_CODE'=>'123456',

	/* 
	| ---------------------------------------------------------
	| Define your table of order and the fields
	| ---------------------------------------------------------
	| 
	*/
	'TABLE_ORDER'                  => NULL,
	'TABLE_FIELD_NO_ORDER'         => NULL,
	'TABLE_FIELD_AMOUNT'           => NULL,
	'TABLE_FIELD_CUSTOMER_NAME'    => NULL,
	'TABLE_FIELD_CUSTOMER_PHONE'   => NULL,
	'TABLE_FIELD_CUSTOMER_EMAIL'   => NULL,
	'TABLE_FIELD_CUSTOMER_ADDRESS' => NULL,
	'TABLE_FIELD_PAYMENT_DATE'     => NULL,
	'TABLE_FIELD_PAYMENT_STATUS'   => NULL,
	'TABLE_FIELD_PAYMENT_CHANNEL'  => NULL,
	'TABLE_FIELD_PAYMENT_APPROVAL_CODE' => NULL,
	'TABLE_FIELD_PAYMENT_SESSION_ID' => NULL,


	/*
	| ---------------------------------------------------------
	| DOKU PAYMENT AVAILABLE CHANNEL 
	| ---------------------------------------------------------
	| 15 = Credit Card
	| 04 = Doku Wallet
	| 02 = Mandiri Clickpay
	| 05 = Permata Bank / ATM Bersama	
	|
	| This setting is for default payment channel otherwise you can set the payment channel on the fly by url parameter "payment_channel"
	*/	
	'AVAILABLE_PAYMENT_CHANNEL'=> ['15','04','02','05'],
	'DEFAULT_PAYMENT_CHANNEL'=> '15', 			


	/* 
	| ---------------------------------------------------------
	| This setting is for set the product name in doku transaction
	| Basicly "DOKULARAVEL" package only send 1 basket to DOKU, that is global invoice. 
	| ---------------------------------------------------------
	| Alias that you can use : 
	| [invoice_no] to generate your invoice number / trans_id 
	| 
	*/
	'PRODUCT_NAME_FORMAT' => 'Invoice For Order No. [invoice_no]',




	/* 
	| ---------------------------------------------------------
	| Set redirect page DOKU 
	| ---------------------------------------------------------
	| [Default] or SHOW_DOKU_SUCCESS_PAGE set TRUE, SHOW_FINISH_PAGE set TRUE, YOUR_OWN_FINISH_PAGE set NULL
	| - Payment Flow : USER DATA -> PROCESSING -> DOKU SUCCESS PAGE -> FINISH PAGE
	|
	| If SHOW_DOKU_SUCCESS_PAGE set TRUE, SHOW_FINISH_PAGE set FALSE, YOUR_OWN_FINISH_PAGE set NULL
	| - Payment Flow : USER DATA -> PROCESSING -> DOKU SUCCESS PAGE
	| 
	| If SHOW_DOKU_SUCCESS_PAGE set FALSE, SHOW_FINISH_PAGE set TRUE, YOUR_OWN_FINISH_PAGE set NULL
	| - Payment Flow : USER DATA -> PROCESSING -> FINISH PAGE
	| 
	| If SHOW_DOKU_SUCCESS_PAGE set FALSE, SHOW_FINISH_PAGE set TRUE, YOUR_OWN_FINISH_PAGE set not NULL / set your own URL PAGE
	| - Payment Flow : USER DATA -> PROCESSING -> YOUR OWN FINISH PAGE
	| 
	*/
	'SHOW_DOKU_SUCCESS_PAGE' => TRUE, //it means the page that generated from DOKU
	'SHOW_FINISH_PAGE'       => TRUE, //it means the page that generated from "dokularavel" package.
	'YOUR_OWN_FINISH_PAGE'   => NULL, //it means the page that generated by your self


	/* 
	| ---------------------------------------------------------
	| This setting is for Develope Mode only, you can view DOKULARAVEL Session, also doPrePayment, or doPayment response
	| ---------------------------------------------------------
	| Access the debug url at /debug
	| 
	| will be available if DEBUG_MODE set TRUE
	|
	*/
	'DEBUG_MODE' => FALSE,
];
```
Silahkan anda atur terlebih dahulu konfigurasi diatas sesuai dengan penjelasan yang ada diatas :) .
## 5. Uji coba
Untuk mengakses halaman DokuLaravel ini ada 2 parameter yang harus anda lengkapi pada paramete URL .   
Base URL : /dokularavel (ini default PAYMENT_PATH bisa anda ganti di konfigurasi)  
Parameter 1 : trans_id (ini adalah nomor transaksi pada table anda sendiri)  
Parameter 2 : payment_channel (ini adalah jenis pembayaran, anda bisa pilih angka berapa sesuai penjelasan diatas, jika dikosongi atau param ini tidak disertakan, maka halaman pertama akan muncul pilihan jenis pembayaran)    
**Contoh URL :**  
```
http://localhost/projek_anda/public/dokularavel?trans_id=INV0001
```
Atau anda ingin memilih payment_channel secara langsung, tinggal tambahkan parameter payment_channel, contoh : 
```
http://localhost/projek_anda/public/dokularavel?trans_id=INV0001&payment_channel=04
```

## 6. Fungsi HOOK
Pada package "DokuLaravel" ini disediakan fitur HOOK dimana anda bisa menjalankan perintah apapun ketika pembayaran selesai atau sebelum pembayaran. Ada sebuah controller bernama "DokuLaravelHookController.php" di directory controller laravel anda. Terdapat 2 method yakni **beforePayment** dan **afterPayment**.  
### 1. **beforePayment($data)**  
Anda bisa menambahkan perintah di dalam method ini, akan dijalankan sebelum pembayaran dilakukan atau berada pada halaman index DokuLaravel. Variabel array **$data** dimana didalamnya terdapat *values* yang bisa anda manfaatkan. Keterangan lebih detail anda bisa buka file *HOOK* tersebut.  
### 2. **afterPayment($status,$dataPayment)**  
Anda bisa menambahkan perintah didalam method ini, akan dijalankan sesudah pembayaran selesai dilakukan. **$status** merupakan variable *boolean* yang menandakan apakah pembayaran berhasil atau tidak. **$dataPayment** merupakan variable *array* yang berisi *values* yang bisa anda manfaatkan lebih lanjut buka file *HOOK* tersebut.
## URL Notify
URL ini digunakan untuk memberikan akses DOKU untuk memberikan notifikasi ke Server kita biasanya untuk jenis pembayaran tertentu seperti Permata Bank, anda bisa menuliskan format URL berikut di dashboard Merchant Doku anda pada kolom "URL Notify" :
```
http://yourdomain.com/dokularavel/notify/[NOTIFY_SCREET_CODE]
```
[NOTIFY_SCREET_CODE] dapat anda atur pada file konfigurasi. Digunakan untuk alasan keamanan aktifitas tidak diinginkan.
## Jenis Pembayaran Yang Tersedia (Payment Channel)
Adapun jenis - jenis pembayaran yang tersedia saat ini di DOKU dan yang terintegrasi pada "DokuLaravel" package ini yakni :   
- 15 = Credit Card
- 04 = Doku Wallet
- 02 = Mandiri Clickpay
- 05 = Permata Bank / ATM Bersama

## Studi Kasus Penggunaan
1. Mobile Apps, Bagi anda yang membutuhkan dalam penggunaan Aplikasi Mobile, anda tinggal membuat **WebView** dan memasukkan URL DokuLaravel diatas
2. Website, Bagi anda yang membutuhkan dalam penggunaan Website, anda tinggal memberikan **link** dan diarahkan ke URL DokuLaravel diatas
