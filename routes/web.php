<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/about', 'HomeController@about');
Route::get('/contacts', 'HomeController@contacts');
//--------------------------

Route::get('/news', 'FrontController@showAllPosts');
Route::get('/news/{id}', 'FrontController@openPost');
//--------------------------

/**
 * Руты админки
 */
Route::group(['prefix' => 'admin', 'middleware' => 'admin.access'], function () {
    Route::get('/', 'AdminController@index');

    // Геймеры
    Route::resource('gamers', 'GamerController');
    Route::post('gamerScoreUpdate', 'GamerController@scoreUpdate');

    // Команды
    Route::resource('teams', 'TeamController');
    Route::post('teamsScoreUpdate', 'TeamController@scoreUpdate');

    // Посты/новости
    Route::resource('posts', 'PostController');

    // Заявки на создание команды
    Route::resource('teamCreateRequests', 'TeamCreateRequestController');

});

//Route::get('/syncTeams', 'AjaxController@syncTeams');

/**
 * Рейтинги во фронте
 */
Route::group(['prefix' => 'rating'], function () {
    Route::get('/gamers/{game?}', 'FrontController@gamerRating');
    Route::get('/teams/{game?}', 'FrontController@teamRating');
});

/**
 * Регистрации участников и команд
 */
Route::group(['prefix' => 'register'], function () {

    Route::get('/gamer', 'GamerController@registerForm');
    Route::post('/gamer', 'GamerController@createGamerAccount');
    Route::get('/gamer/result', 'GamerController@displayGamerRegisterResult');

    Route::get('/team', 'GamerController@registerTeamForm');

});

/**
 * Аякс-руты
 */
Route::group(['prefix' => 'ajax'], function() {
    Route::post('/search-gamer', 'GamerController@searchGamerForDuplicate');
});

/**
 * Авторизационные пути. Вынес чтобы были очевидны и наглядны
 */

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');



