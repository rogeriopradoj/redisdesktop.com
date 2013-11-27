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

    $content =  require APP_PATH . '/views/download.phtml';
    $layout = require APP_PATH . '/views/layout.phtml';

    return $layout;
};