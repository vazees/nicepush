<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new ConsoleCommandTool\Cli();

$app->addCommand(new Application\CustomCommand\Demonstrate());

$app->run();