<?php
/**
 * redisdesktop.com site
 *
 * @author Igor Malinovskiy <glide.name>
 * @file index.php
 * @date: 9/26/13
 */

define("APP_PATH",   __DIR__ . '/../app');

if (!file_exists(APP_PATH . '/data/rdm.json')) {
    exit('Create application settings file: <code> cp /app/data/rdm.sample.json /app/data/rdm.json</code>');
}

require_once __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../vendor/predis/predis/autoload.php';

$rdmData = json_decode(file_get_contents(APP_PATH . '/data/rdm.json'), true);

$app = new Silex\Application();

//main page
$app->get('/', function () use ($rdmData) {

    $title = "Redis Desktop Manager - Redis GUI management tool for Windows, Mac OS X, Ubuntu and Debian.";
    $description = "Cross-platform redis desktop manager - desktop management GUI for mac os x, windows, debian and ubuntu.";

    $content = require APP_PATH . '/views/main.phtml';
    $layout = require APP_PATH . '/views/layout.phtml';

    return $layout;
});

$app->get('/download', function () use ($rdmData) {

    $title = "Download Redis Desktop Manager";
    $description = "Download Redis Desktop Manager for mac os x, windows, debian and ubuntu.";

    $content =  require APP_PATH . '/views/download.phtml';
    $layout = require APP_PATH . '/views/layout.phtml';

    return $layout;
});

$app->get('/contribute', function () use ($rdmData) {

    $title = "Contribute to Redis Desktop Manager";
    $description = "Contribute to Redis Desktop Manager";

    $content =  require APP_PATH . '/views/contribute.phtml';

    $layout = require APP_PATH . '/views/layout.phtml';

    return $layout;
});

$app->get('/get-update', function () use ($rdmData) {

    if (@version_compare($_GET['version'], $rdmData['version']) == -1) {
        echo "<a href='http://redisdesktop.com/download'>{$rdmData['version']}</a>";
    }

    if (!isset($rdmData['db']) && !empty($rdmData['db'])) {
        return '';
    }

    Predis\Autoloader::register();

    $redis = new Predis\Client($rdmData['db']);

    $currDate = date("d-m-Y");
    $redis->sadd("stats:{$currDate}:activeUsers", $_SERVER['REMOTE_ADDR']);

    return '';
});

$app->run();