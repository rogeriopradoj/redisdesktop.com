<?php
/**
 * redisdesktop.com site
 *
 * @author Igor Malinovskiy <glide.name>
 * @file index.php
 * @date: 9/26/13
 */

define("APP_PATH",   realpath(__DIR__ . '/../app'));

if (!file_exists(APP_PATH . '/data/rdm.json')) {
    exit('Create application settings file: <code> cp /app/data/rdm.sample.json /app/data/rdm.json</code>');
}

require_once __DIR__.'/../vendor/autoload.php';
require APP_PATH.'/../vendor/predis/predis/autoload.php';

$rdmData = json_decode(file_get_contents(APP_PATH . '/data/rdm.json'), true);

$app = new Silex\Application();

/**
 * set silex debug mode
 */
$app['debug'] = $rdmData['debug'];

$app->register(new \CHH\Silex\CacheServiceProvider, array(
    'cache.options' => array("default" => array(
        "driver" => "filesystem",
        "directory" => APP_PATH . '/cache/'
    ))
));

//site controllers
$app->get('/', require APP_PATH . '/controllers/index.php');
$app->get('/download', require APP_PATH . '/controllers/download.php');
$app->get('/contribute', require APP_PATH . '/controllers/contribute.php');
$app->get('/get-update', require APP_PATH . '/controllers/get-update.php');
$app->get('/community', require APP_PATH . '/controllers/community.php');
$app->post('/crash-report', require APP_PATH . '/controllers/crash-report.php');

$app->run();