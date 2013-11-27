<?php
/**
 * todo: description
 *
 * @author Igor Malinovskiy <glide.name>
 * @file contribute.php
 * @date: 27.11.13
 */

return function () use ($rdmData) {

    $title = "Contribute to Redis Desktop Manager";
    $description = "Contribute to Redis Desktop Manager";

    $content =  require APP_PATH . '/views/contribute.phtml';

    $layout = require APP_PATH . '/views/layout.phtml';

    return $layout;
};