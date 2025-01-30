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
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (!isset($args['id']))
        {
            $error = 'Film id not provided';
        }
        elseif (!is_int($args['id']))
        {
            $error = 'Film id is not an integer';
        }
        if($error)
        {
            return $this->respondWithJson($response, ([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => ['id' => $error]
            ]));
        }
        try {
            $id = $this->model->deleteFilm($args['id']);

            if(empty($id))
            {
                return $this->respondWithJson($response, ['message' => 'Film not found.'], 404);
            }
            return $this->respondWithJson($response, ['message' => 'Film successfully deleted.'], 200);
        } catch (\PDOException $e) {
            return $this->respondWithJson($response, ['error' => $e->getMessage()], 500);
        }
    }
}