<?php

declare(strict_types=1);

use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\Filesystem\Filesystem;

require __DIR__ . '/../vendor/autoload.php';

new Filesystem()->remove(__DIR__ . '/var');

ErrorHandler::register(null, false);
