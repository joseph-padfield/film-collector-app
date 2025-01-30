<?php

namespace App\Interfaces;

interface FilmsModelInterface
{
    public function addFilm(array $newFilm): int;
    public function getFilms(string $sortBy, string $sortOrder): array;
    public function updateFilm(int $filmId, array $updateInfo): bool;
    public function deleteFilm(int $id): int;
}