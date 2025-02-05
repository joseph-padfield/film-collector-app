<?php

namespace App\Controllers;

use App\Interfaces\FilmsModelInterface;
use App\Abstracts\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddFilmController extends Controller
{
    private FilmsModelInterface $model;

    public function __construct(FilmsModelInterface $model)
    {
        $this->model = $model;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $newFilm = $request->getParsedBody();
        $sanitizedNewFilm = $this->sanitizeFilmData($newFilm);
        $errors = $this->validateFilmData($sanitizedNewFilm);

        if (!empty($errors)) {
            return $this->respondWithJson($response, [
                'status' => 'errors',
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        try {
            $insertedId = $this->model->addFilm($sanitizedNewFilm);

            if ($insertedId > 0) {
                return $this->respondWithJson($response, json_encode(['message' => 'Film added successfully']), 201);
            } else {
                return $this->respondWithJson($response, json_encode(['message' => 'Failed to add new film']), 500);
            }
        } catch (\PDOException $e) {
            return $this->respondWithJson($response, json_encode(['message' => $e->getMessage()]), 500);
        }
    }

    private function validateFilmData(array $data): array
    {
        $errors = [];

//        Title: Required, string, max of 255 characters
        if (!isset($data['title']) || !is_string($data['title']) || empty($data['title'])) {
            $errors['title'] = 'Title is required and must be a string.';
        } elseif (strlen($data['title']) > 255) {
            $errors['title'] = 'Title cannot exceed 255 characters.';
        }
//        Release date: required, valid date format
        if (!isset($data['release_date']) || !is_string($data['release_date']) || empty($data['release_date'])) {
            $errors['release_date'] = 'Release date is required and must be a string.';
        } else {
            $date = \DateTime::createFromFormat('Y-m-d', $data['release_date']);
            if (!$date || $date->format('Y-m-d') !== $data['release_date']) {
                $errors['release_date'] = 'Release date must be a valid date. Use YYY-MM-DD format.';
            }
        }

//        Poster: string, required max 255 characters, valid url
        if (!isset($data['poster_path']) || !is_string($data['poster_path']) || empty($data['poster_path'])) {
            $errors['poster_path'] = 'Poster path is required and must be a string.';
        } else {
            if (filter_var($data['poster_path'], FILTER_VALIDATE_URL) === false) {
                $errors['poster_path'] = 'Poster path must be a valid URL.';
            }
        }

        foreach (['tagline' => 255, 'overview' => 1000, 'original_language' => 255, 'cast' => 10000, 'production_countries' => 255, 'director' => 255, 'production_companies' => 255] as $field => $maxLength) {
            if (isset($data[$field]) && is_string($data[$field]) && strlen($data[$field]) > $maxLength) {
                $errors[$field] = ucfirst($field) . ' cannot exceed ' . $maxLength . ' characters.';
            } elseif (isset($data[$field]) && !is_string($data[$field])) {
                $errors[$field] = ucfirst($field) . ' must be a string.';
            }
        }

        if (!is_int($data['runtime'])) {
            $errors['runtime'] = 'Runtime must be an integer.';
        }

        return $errors;

    }

    private function sanitizeFilmData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (gettype($value) === 'string')
            {
                $data[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            }
        }
        return $data;
    }
}