<?php
declare(strict_types=1);

use App\Factories\LoggerFactory;
use App\Factories\PDOFactory;
use App\Factories\RendererFactory;
use App\Models\FilmsModel;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;
use App\Interfaces\FilmsModelInterface;
use App\Models\TMDBModel;
use App\Interfaces\TMDBModelInterface;

return function (ContainerBuilder $containerBuilder) {
    $container = [];

    $container[LoggerInterface::class] = DI\factory(LoggerFactory::class);
    $container[PhpRenderer::class] = DI\factory(RendererFactory::class);
    $container[PDO::class] = DI\factory(PDOFactory::class);
    $container[FilmsModelInterface::class] = DI\autowire(FilmsModel::class);
    $container[TMDBModelInterface::class] = DI\autowire(TMDBModel::class);
    $containerBuilder->addDefinitions($container);
};