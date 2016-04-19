<?php

$autoloadFile = false;
$path         = '/../../../../../vendor/autoload.php';

while (preg_match('~^\/\.\..*$~', $path)) {
    if (is_file(__DIR__ . $path)) {
        $autoloadFile = realpath(__DIR__ . $path);
        break;
    }
    $path = substr($path, 3);
}

if (!$autoloadFile) {
    echo "You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install --dev
";
    exit(1);
}

require $autoloadFile;
