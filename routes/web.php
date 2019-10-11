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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/sports', 'SportsController@store');
Route::patch('/sports/{sport}', 'SportsController@update');
Route::delete('/sports/{sport}', 'SportsController@destroy');

Route::post('/leagues', 'LeaguesController@store');
Route::patch('/leagues/{league}', 'LeaguesController@update');
Route::delete('/leagues/{league}', 'LeaguesController@destroy');
Route::post('/leagues/{league}/teams', 'LeaguesController@addTeam');
Route::delete('/leagues/{league}/teams', 'LeaguesController@removeTeam');

Route::post('/teams', 'TeamsController@store');
Route::patch('/teams/{team}', 'TeamsController@update');
Route::delete('/teams/{team}', 'TeamsController@destroy');

Route::post('/players', 'PlayersController@store');
Route::patch('/players/{player}', 'PlayersController@update');
Route::delete('/players/{player}', 'PlayersController@destroy');

Route::post('/events', 'EventsController@store');
Route::patch('/events/{event}', 'EventsController@update');
Route::delete('/events/{event}', 'EventsController@destroy');
