<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/login', 'LoginController@login');
$router->post('/register', 'UserController@register');
$router->post('/inviteData', ['middleware' => 'auth', 'uses' =>  'InviteUser@invite']);
$router->post('/profileUpdate', ['middleware' => 'auth', 'uses' =>  'Profile@update']);
$router->post('/getProfile', ['middleware' => 'auth', 'uses' =>  'Profile@getProfileInfo']);
$router->post('/userAppOpen', ['middleware' => 'auth', 'uses' =>  'AppOpen@getUserAppOpen']);
$router->post('/offerList', ['middleware' => 'auth', 'uses' =>  'Offers@getAllOffer']);
$router->post('/offerDetails', ['middleware' => 'auth', 'uses' =>  'Offers@getOfferDetails']);
$router->post('/userTransactions', ['middleware' => 'auth', 'uses' =>  'UserTransaction@getUserTransaction']);
$router->post('/walletData', ['middleware' => 'auth', 'uses' =>  'WalletData@getAllWalletData']);
$router->post('/offerClicked', ['middleware' => 'auth', 'uses' =>  'OfferClick@clickOffer']);
$router->post('/invite', ['middleware' => 'auth', 'uses' =>  'InviteUser@invite']);
$router->post('/spinWheel', ['middleware' => 'auth', 'uses' =>  'UserContest@spinContest']);
$router->post('/scratchCard', ['middleware' => 'auth', 'uses' =>  'UserContest@scratchCard']);
$router->post('/diesRoller', ['middleware' => 'auth', 'uses' =>  'UserContest@diesRoller']);
$router->post('/watchVideo', ['middleware' => 'auth', 'uses' =>  'UserContest@watchVideo']);
$router->post('/redeemCash', ['middleware' => 'auth', 'uses' =>  'RedeemCoin@withdrawAmount']);
$router->post('/convertDiamondToCoin', ['middleware' => 'auth', 'uses' =>  'RedeemCoin@transferDiamond']);
$router->post('/contestCounter', ['middleware' => 'auth', 'uses' =>  'ContestCounter@index']);
$router->get('/offerComplete', 'OfferTracking@index');
$router->post('/getGames', ['middleware' => 'auth', 'uses' => 'Games@getAllGames']);
$router->post('/getActiveContest', ['middleware' => 'auth', 'uses' => 'ContestController@index']);
$router->post('/submitContestAnswer', ['middleware' => 'auth', 'uses' => 'ContestController@contestAnswer']);
$router->post('/getLeaderBoardRanking', ['middleware' => 'auth', 'uses' => 'LeaderBoardController@index']);
$router->post('/getVideos', ['middleware' => 'auth', 'uses' => 'VideoListing@getAllVideos']);
$router->post('/getMiniBanner', ['middleware' => 'auth', 'uses' => 'MiniBanner@getAllBanners']);


//without Auth
$router->post('/offerListOpen',['uses' =>  'Offers@getAllOffer']);
$router->post('/offerDetailsOpen', ['uses' =>  'Offers@getOfferDetails']);

$router->post('/getNews',['uses' =>  'NewsController@getAllNews']);