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
    $platform = strtolower(preg_replace("/[^a-z ]/i", '', $request->request->get('platform', 'unknownPlatform')));
    $productName = strtolower(preg_replace("/[^a-z ]/i", '', $request->request->get('product', 'unknownProduct')));
    $version = strtolower(preg_replace('/[^0-9\.\-a-z]/i', '', $request->request->get('version', '0.0.0')));

    // if old version crashed - don't create issue
    if (@version_compare($version, '0.7.7.50' /*$rdmData['version']*/) == -1) {
        return 'https://github.com/uglide/RedisDesktopManager/releases';
    }

    $dumpFileName = "{$productName}_{$version}_{$platform}_{$currDateTime}.dmp";

    $uploadPath = array(
        APP_PATH, $rdmData['crashReportsDir'],
        $dumpFileName
    );

    $dumpName = implode('/', $uploadPath);

    if (!move_uploaded_file($_FILES['upload_file_minidump']['tmp_name'], $dumpName)) {
        return 'error';
    }

    $client = new Github\Client();
    $client->authenticate($rdmData['githubAuth'], '', Github\Client::AUTH_URL_TOKEN);
    $issueInfo = $client->api('issue')->create(
        'uglide', 'RedisDesktopManager',
        array(
            'title' => "Crash report #{$currDateTime}-" . rand(1, 100000),
            'body' => "RDM version: {$version} \nPlatform: {$platform} \n Crash dump: {$dumpFileName}",
            'labels' => array("crash-report")
        )
    );

    $currDate = date("d-m-Y");

    Predis\Autoloader::register();
    $redis = new Predis\Client($rdmData['db']);
    $redis->sadd("stats:{$currDate}:crashreports", $_SERVER['REMOTE_ADDR']);

    $task = array('minidump' => $dumpFileName, 'issue' => $issueInfo['number']);
    $redis->lpush("breakpad:unprocessed", json_encode($task));

    return $issueInfo['html_url'];
};