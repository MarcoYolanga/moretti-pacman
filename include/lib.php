<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


/**
 * Manage file stat cache
 */
$GLOBALS['file_stat_cache_cleared'] = false;
function _filemtime($f)
{
    if (!$GLOBALS['file_stat_cache_cleared']) {
        clearstatcache();
        $GLOBALS['file_stat_cache_cleared'] = true;
    }

    return filemtime($f);
}

/**
 * Smart import js & css
 */
function import($externals, $disable_versioning = false)
{
    $allow_minified_assets = true;
    if (gettype($externals) == "string")
        $externals = [$externals];
    foreach ($externals as $external) {
        if (!is_file($external)) {
            echo "failed import: $external not found";
            continue;
        }
        $ext = pathinfo($external, PATHINFO_EXTENSION);
        if (!$disable_versioning) {
                $external .= "?debug=" . _filemtime($external);
           
        }
        switch ($ext) {
            case 'js':
                if ($allow_minified_assets) {
                    $minified = str_replace('.js', '.min.js', $external);
                    if (is_file(explode('?', $minified)[0]))
                        $external = $minified;
                    //else echo "minified file not found: $minified";
                }
                echo '<script src="', $external, '" charset="utf-8" type="text/javascript"></script>';
                break;
            case 'css':
                if ($allow_minified_assets) {
                    $minified = str_replace('.css', '.min.css', $external);
                    if (is_file(explode('?', $minified)[0]))
                        $external = $minified;
                    //else echo "minified file not found: $minified";
                }
                echo '<link href="', $external, '" rel="stylesheet" type="text/css">';
                break;
            default:
                echo "Can't import the $ext file: $external ";
                break;
        }
    }
}
