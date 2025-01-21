<?php

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Models\FilmsModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FilmsController extends Controller
{
    private FilmsModel $model;

    public function __construct(FilmsModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $films = $this->model->getFilms();

            if(empty($films)) {
                return $this->respondWithJson($response, ['message' => 'No films found.'], 204);
            }
            return $this->respondWithJson($response, $films, 200);
        } catch (\PDOException $e) {
            return $this->respondWithJson($response, ['error' => $e->getMessage()], 500);
        }
    }
}