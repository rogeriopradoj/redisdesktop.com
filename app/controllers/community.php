<?php
/**
 * todo: description
 *
 * @author Igor Malinovskiy <glide.name>
 * @file community.php
 * @date: 09.12.13
 */

return function () use ($rdmData) {

    $title = "Community - Redis Desktop Manager";
    $description = "Community";

    $content =  require APP_PATH . '/views/community.phtml';

    $layout = require APP_PATH . '/views/layout.phtml';

    return $layout;
};
