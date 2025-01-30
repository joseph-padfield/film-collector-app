<?php

namespace App\Controllers;

use App\Models\FilmsModel;
use App\Abstracts\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteFilmController extends Controller
{
    private FilmsModel $model;

    public function __construct(FilmsModel $model)
    {
        $this->model = $model;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $this->model->deleteFilm($args['id']);

            if(empty($id))
            {
                return $this->respondWithJson($response, ['message' => 'Film not found.'], 404);
            }
            return $response->withHeader('Content-Type', '/films')->withStatus(200);
        } catch (\PDOException $e) {
            return $this->respondWithJson($response, ['error' => $e->getMessage()], 500);
        }
    }
}