<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//Rutas protegidas por la auth
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user-profile', 'AuthController@userProfile');

    Route::get('teams', 'TeamsController@index');
    Route::get('teams/{team}', 'TeamsController@show');
    Route::post('teams', 'TeamsController@store');
    Route::patch('teams/{team}', "TeamsController@update");
    Route::delete('teams/{team}', 'TeamsController@destroy');

    Route::get('players', 'PlayersController@index');
    Route::get('players/{player}', 'PlayersController@show');
    Route::post('players', 'PlayersController@store');

    Route::get('negotiations', 'NegotiationsController@index');
    Route::get('negotiations/{team}', 'NegotiationsController@show');
    Route::get('negotiations/incoming/{team}', 'NegotiationsController@showIncoming');
    Route::get('negotiations/outgoing/{team}', 'NegotiationsController@showOutgoing');
    Route::post('negotiations', 'NegotiationsController@store');
    Route::patch('negotiations/accept/{negotiation}', 'NegotiationsController@accept');
    Route::patch('negotiations/reject/{negotiation}', 'NegotiationsController@reject');

    Route::get('clauses', 'ClausesController@index');
    Route::get('clauses/incoming/{team}', 'ClausesController@showIncoming');
    Route::get('clauses/outgoing/{team}', 'ClausesController@showOutgoing');
    Route::post('clauses', 'ClausesController@store');
    Route::post('clauses/launch/{clause}', 'ClausesController@launch');
    Route::patch('clauses/accept/{clause}', 'ClausesController@accept');
    Route::patch('clauses/reject/{clause}', 'ClausesController@reject');

    Route::get('competitions/{competition}', 'CompetitionsController@show');
    Route::get('competitions/table/{competition}', 'CompetitionsController@clasificationTable');
    Route::get('competitions/rounds/{competition}', 'CompetitionsController@showRounds');
    Route::get('competitions/stats/{competition}', 'CompetitionsController@showStats');
    Route::post('competitions', 'CompetitionsController@store');
    Route::patch('competitions/{competition}/open-round', 'CompetitionsController@openRound');
    Route::post('competitions/post', 'CompetitionsController@postResult');
    //Route::patch('competitions/confirm', 'CompetitionsController@confirmResult');
});





