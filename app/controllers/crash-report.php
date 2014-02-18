<?php
/**
 * todo: description
 *
 * @author Igor Malinovskiy <glide.name>
 * @file crash-report.php
 * @date: 09.12.13
 */

use Symfony\Component\HttpFoundation\Request;


return function (Request $request) use ($rdmData) {

    /**
     * @var $request Symfony\Component\HttpFoundation\Request
     */
    if ($request->files->count() == 0) {
        return '';
    }

    $currDateTime = date("d-m-Y_G-i");
    $platform = preg_replace("/[^a-z ]/i", '', $request->request->get('platform', 'unknownPlatform'));
    $productName = preg_replace("/[^a-z ]/i", '', $request->request->get('product', 'unknownProduct'));
    $version = preg_replace('/[^0-9\.]/i', '', $request->request->get('version', '0.0.0'));

    $dumpFileName = "{$productName}_v{$version}_{$currDateTime}_{$platform}.dmp";

    $uploadPath = array(
        APP_PATH, $rdmData['crashReportsDir'],
        $dumpFileName
    );

    $dumpName = implode('/', $uploadPath);

    if (!move_uploaded_file($_FILES['upload_file_minidump']['tmp_name'], $dumpName)) {
        return 'error';
    }

    // if old version crashed - don't create issue
    if (@version_compare($version, $rdmData['version']) == -1) {
        return 'https://github.com/uglide/RedisDesktopManager/releases';
    }

    $client = new Github\Client();
    $client->authenticate($rdmData['githubAuth'], '', Github\Client::AUTH_URL_TOKEN);
    $issueInfo = $client->api('issue')->create(
        'uglide', 'RedisDesktopManager',
        array(
            'title' => "Crash report #{$currDateTime}-" . rand(1, 100000),
            'body' => "RDM version: {$version} \nPlatform: {$platform} \n Crash dump: {$dumpFileName}"
        )
    );

    $currDate = date("d-m-Y");

    Predis\Autoloader::register();
    $redis = new Predis\Client($rdmData['db']);
    $redis->sadd("stats:{$currDate}:crashreports", $_SERVER['REMOTE_ADDR']);

    return $issueInfo['html_url'];
};