<?php
/**
 * todo: description
 *
 * @author Igor Malinovskiy <glide.name>
 * @file get-update.php
 * @date: 27.11.13
 */

return function () use ($rdmData) {

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
};