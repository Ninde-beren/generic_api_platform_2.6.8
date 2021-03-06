<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();

    // Define what rule sets will be applied
    $containerConfigurator->import(SetList::DEAD_CODE);
//    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::PHP_55);
    $containerConfigurator->import(SetList::PHP_56);
    $containerConfigurator->import(SetList::PHP_70);
    $containerConfigurator->import(SetList::PHP_71);
    $containerConfigurator->import(SetList::PHP_73);
    $containerConfigurator->import(SetList::PHP_74);
    $containerConfigurator->import(SetList::PHP_80);
    $containerConfigurator->import(\Rector\Doctrine\Set\DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES);
    $containerConfigurator->import(\Rector\Symfony\Set\SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES);
    $containerConfigurator->import(\Rector\Nette\Set\NetteSetList::ANNOTATIONS_TO_ATTRIBUTES);
    $containerConfigurator->import(\Rector\Symfony\Set\SensiolabsSetList::FRAMEWORK_EXTRA_61);


    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
