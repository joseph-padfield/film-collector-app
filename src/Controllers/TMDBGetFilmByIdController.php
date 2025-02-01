<?php

namespace App\Controllers;

use App\Abstracts\Controller;
use App\Interfaces\TMDBModelInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TMDBGetFilmByIdController extends Controller
{
    private TMDBModelInterface $model;

    public function __construct(TMDBModelInterface $model)
    {
        $this->model = $model;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];

        if (!ctype_digit($id) || $id < 1) {
            return $this->respondWithJson($response, [
                'status' => 'Error',
                'message' => 'Validation failed',
                'errors' => ['ID must be a positive integer.']
            ], 400);
        }

        $apiResponse = $this->model->searchFilmId($id);

        if (!$apiResponse) {
            return $this->respondWithJson($response, [
                'status' => 'Error',
                'message' => 'API error',
                'errors' => ['Failed to fetch data from TMDB API.']
            ], 500);
        }

        $data = json_decode($apiResponse, true);

        if (empty($data) || !isset($data)) {
            return $this->respondWithJson($response, [
                'status' => 'Error',
                'message' => 'Not found',
                'errors' => ['Film not found.']
            ], 404);
        }

        $filmDetails = $data[0];
        $credits = $data[1];

        $requiredKeys = [
            'title', 'release_date', 'poster_path', 'tagline',
            'overview', 'original_language', 'production_countries',
            'production_companies', 'runtime'
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $filmDetails)) {
                return $this->respondWithJson($response, [
                    'status' => 'Error',
                    'message' => 'Invalid data',
                    'errors' => ['Invalid  film details data structure received from API. Missing: ' . $key]
                ], 500);
            }
        }

        [
            'title' => $title,
            'release_date' => $release_date,
            'poster_path' => $poster_path,
            'tagline' => $tagline,
            'overview' => $overview,
            'original_language' => $original_language,
            'production_countries' => $production_countries,
            'production_companies' => $production_companies,
            'runtime' => $runtime,
        ] = $filmDetails;

        $director = [];

        if (!isset($credits) || !is_array($credits)) {
            return $this->respondWithJson($response, [
                'status' => 'Error',
                'message' => 'Invalid data',
                'errors' => ['Invalid  credits data structure received from API.']
            ], 500);
        }

        $requiredKeys = ['crew', 'cast'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $credits) || !is_array($credits[$key])) {
                return $this->respondWithJson($response, [
                    'status' => 'Error',
                    'message' => 'Invalid data',
                    'errors' => ['Invalid' . $key . 'data API response.']
                ], 500);
            }
        }

        foreach ($credits['crew'] as $crewMember) {
            if (!is_array($crewMember)) {
                return $this->respondWithJson($response, [
                    'status' => 'Error',
                    'message' => 'Invalid data',
                    'errors' => ['Invalid crew data API response:' . print_r($crewMember, true)]
                ], 500);
            }

            $requiredKeys = ['job', 'name'];

            foreach ($requiredKeys as $key) {
                if (!array_key_exists($key, $crewMember)) {
                    return $this->respondWithJson($response, [
                        'status' => 'Error',
                        'message' => 'Invalid data',
                        'errors' => ['Invalid data structure received from API.']
                    ], 500);
                }
            }
        }

        foreach ($credits['crew'] as $crewMember) {
            if ($crewMember['job'] == 'Director') {
                $director[] = $crewMember['name'];
            }
        }

        $directorListString = json_encode($director);

        $castList = [];

        foreach ($credits['cast'] as $cast) {
            $castList[] = $cast['name'];
        }

        $castListString = json_encode($castList);

        $production_countries_names = [];
        foreach ($production_countries as $country) {
            $production_countries_names[] = $country['name'];
        }

        $production_countriesListString = json_encode($production_countries_names);

        $production_companies_names = [];
        foreach ($production_companies as $company) {
            $production_companies_names[] = $company['name'];
        }

        $production_companiesListString = json_encode($production_companies_names);

        $infoToPass =
            [
                'title' => $title,
                'release_date' => $release_date,
                'poster_path' => 'https://image.tmdb.org/t/p/w500' . $poster_path,
                'tagline' => $tagline,
                'overview' => $overview,
                'original_language' => $original_language,
                'production_countries' => $production_countriesListString,
                'director' => $directorListString,
                'production_companies' => $production_companiesListString,
                'runtime' => $runtime,
                'cast' => $castListString,
            ];

        $response->getBody()->write(json_encode($infoToPass));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}