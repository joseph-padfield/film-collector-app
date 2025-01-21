<?php

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Models\TMDBModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TMDBGetFilmByIdController extends Controller
{
    private TMDBModel $model;

    public function __construct(TMDBModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $apiResponse = $this->model->searchFilmId($id);

        $data = json_decode($apiResponse, true);

        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}