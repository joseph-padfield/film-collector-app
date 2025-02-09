<?php
declare(strict_types=1);

use App\Controllers\DeleteFilmController;
use Slim\App;
use Slim\Views\PhpRenderer;
use App\Controllers\FilmsController;
use App\Controllers\AddFilmController;
use App\Controllers\UpdateFilmController;
use App\Controllers\TMDBSearchFilmsController;

return function (App $app) {

    $app->add(function($request, $handler)
    {
       $response = $handler->handle($request);
       return $response
           ->withHeader('Access-Control-Allow-Origin', '*')
           ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
           ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });

    $container = $app->getContainer();

    $app->get('/', function ($request, $response, $args) use ($container) {
        $renderer = $container->get(PhpRenderer::class);
        return $renderer->render($response, "index.php", $args);
    });

    $app->get('/films', FilmsController::class);
    $app->post('/films', AddFilmController::class);
    $app->delete('/films/{id}', DeleteFilmController::class);
    $app->put('/films/{id}', UpdateFilmController::class);

    // TMDB routes
    $app->get('/searchFilm', TMDBSearchFilmsController::class);
    $app->get('/searchFilm/{id}', \App\Controllers\TMDBGetFilmByIdController::class);

};