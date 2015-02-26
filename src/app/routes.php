<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('ajax/ward/long_term_voids', 'AjaxController@longTermVoidsByWard');

Route::get('ajax/pois', 'AjaxController@showPois');

Route::get('ajax/property/info', 'AjaxController@propertyInfo');
