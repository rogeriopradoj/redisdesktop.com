<?php
/**
 * todo: description
 *
 * @author Igor Malinovskiy <glide.name>
 * @file download.php
 * @date: 27.11.13
 */

return function () use ($rdmData) {

    $title = "Download Redis Desktop Manager";
    $description = "Download Redis Desktop Manager for mac os x, windows, debian and ubuntu.";

    $builds = getUnstableBuildList($rdmData['version']);

    $content =  require APP_PATH . '/views/download.phtml';
    $layout = require APP_PATH . '/views/layout.phtml';

    return $layout;
};

function getUnstableBuildList($version) {

    $buildsDir = PUBLIC_PATH . '/builds';

    if (!is_dir($buildsDir))
        return array();


    $result = array();
    $files = scandir($buildsDir, 1);
    $version++;
    $version = str_replace('.', '\.', $version);

    foreach ($files as $file) {

        if (preg_match('/^.+('. $version .').+\.(exe|deb|dmg)$/', $file) === 0)
            continue;

        $result[] = $file;
    }

    return $result;
}