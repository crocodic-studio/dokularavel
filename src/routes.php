<?php


$namespace = '\crocodicstudio\dokularavel\Controllers';
Route::group(['middleware'=>[],'prefix'=>'dokularavel','namespace'=>$namespace], function () {		
	Route::get('/', ['uses'=>'DokuController@index','as'=>'DokuController.index']);
	Route::get('finish/{invoice_no}/{amount}', ['uses'=>'DokuController@finish','as'=>'DokuController.finish']);
	Route::post('pay', ['uses'=>'DokuController@pay','as'=>'DokuController.pay']);
});