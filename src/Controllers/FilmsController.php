<?php

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Interfaces\FilmsModelInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Exception\HttpBadRequestException;

class FilmsController extends Controller
{
    private FilmsModelInterface $model;
    private PhpRenderer $renderer;

    public function __construct(FilmsModelInterface $model, PhpRenderer $renderer)
    {
        $this->model = $model;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $acceptedSortTerms = ['title', 'year', 'director', 'date_watched', 'favourite'];
        $acceptedOrderTerms = ['asc', 'desc'];

        try {
            $queryParams = $request->getQueryParams();

            $sortBy = $queryParams['sortBy'] ?? 'date_watched';
            $sortOrder = $queryParams['sortOrder'] ?? 'desc';

            $sortBy = filter_var($sortBy, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $sortOrder = filter_var($sortOrder, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!in_array($sortBy, $acceptedSortTerms))
            {
                throw new HttpBadRequestException($request, 'Invalid sort term. Valid sort terms: ' . implode(', ', $acceptedSortTerms));            }
            if (!in_array($sortOrder, $acceptedOrderTerms))
            {
                throw new HttpBadRequestException($request, 'Invalid sort order. Valid sort orders: ' . implode(', ', $acceptedOrderTerms));
            }

            $films = $this->model->getFilms($sortBy, $sortOrder);

            if(empty($films)) {
                return $this->respondWithJson($response, ['message' => 'No films found.'], 204);
            }

            return $this->renderer->render($response, 'homepage.phtml', ['films'=>$films]);

        } catch (HttpBadRequestException $e)
        {
            return $this->respondWithJson($response, ['error' => $e->getMessage()], $e->getCode() ?? 400);
        } catch (\PDOException $e)
        {
            error_log($e);
            return $this->respondWithJson($response, ['error' => 'Internal server error'], 500);
        } catch (\Exception $e)
        {
            error_log($e);
            return $this->respondWithJson($response, ['error' => 'An unexpected error occurred.'], 500);
        }
    }
}