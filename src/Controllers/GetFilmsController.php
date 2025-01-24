<?php

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Models\FilmsModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetFilmsController extends Controller
{
    private FilmsModel $model;

    public function __construct(FilmsModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $acceptedSortTerms = ['title', 'year', 'director', 'date_watched', 'favourite'];
        $acceptedOrderTerms = ['ASC', 'DESC'];

        try {
            $sortBy = $args['sortBy'] ?? 'title';
            $sortOrder = $args['sortOrder'] ?? 'ASC';

            if (!in_array($sortBy, $acceptedSortTerms))
            {
                return $this->respondWithJson($response, ['message' => 'Invalid sort term.'], 400);
            }
            if (!in_array($sortOrder, $acceptedOrderTerms))
            {
                return $this->respondWithJson($response, ['message' => 'Invalid sort order.'], 400);
            }

            $films = $this->model->getFilms($sortBy, $sortOrder);

            if(empty($films)) {
                return $this->respondWithJson($response, ['message' => 'No films found.'], 204);
            }

            return $this->respondWithJson($response, $films, 200);
        } catch (\PDOException $e) {
            return $this->respondWithJson($response, ['error' => 'Internal server error'], 500);
        }
    }
}