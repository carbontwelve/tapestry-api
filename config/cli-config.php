<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

if (! ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}

require __DIR__.'/../vendor/autoload.php';

$settings = include __DIR__.'/../config/app.php';
$settings = $settings['doctrine'];

$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
    $settings['meta']['entity_path'],
    $settings['meta']['auto_generate_proxies'],
    $settings['meta']['proxy_dir'],
    $settings['meta']['cache'],
    false
);

$em = \Doctrine\ORM\EntityManager::create($settings['connection'], $config);

\Doctrine\DBAL\Types\Type::addType('uuid', 'Ramsey\Uuid\Doctrine\UuidType');

return ConsoleRunner::createHelperSet($em);