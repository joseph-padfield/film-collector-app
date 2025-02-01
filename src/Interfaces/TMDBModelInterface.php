<?php

namespace App\Interfaces;

interface TMDBModelInterface
{
    public function searchFilmTitle(string $userInput, int $page=1);
    public function searchFilmId(int $id);
}