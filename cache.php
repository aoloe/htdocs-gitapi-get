<?php
/**
 * Write and read cache files.
 *
 * - All files are stored in the same directory.
 * - You can give paths as filenames and they will be urlencoded.
 * 
 * TODO:
 * - remove the old get_cache and put_cache once the new ones are working for the manuals
 * - probably remove the GITAPIGET_CACHE_PATH constant.
 */

define('GITAPIGET_CACHE_LIB', true);

if (!defined('GITAPIGET_CACHE_PATH')) {
    define('GITAPIGET_CACHE_PATH', 'cache/');
}

/**
 * TODO: probably move this to a different place... it's not specific to the cache
 */
function ensure_gitapi_directory_writable($path, $base_path = '') {
    // debug('path', $path);
    // debug('base_path', $base_path);
    $result = true;
    if ($base_path != '') {
        $base_path = rtrim($base_path, '/').'/';
        $path = trim(substr($path, count($base_path) -1), '/');
    }
    if (file_exists($base_path.$path)) {
        $result = is_dir($base_path.$path) && is_writable($base_path.$path);
    } else {
        if (!file_exists($base_path)) {
            if (is_writable(dirname($base_path))) {
                mkdir($base_path);
            } else {
                $result = false;
            }
        }
        if ($result) {
            $result = true;
            $path_item = $base_path;
            if (is_writable($path_item)) {
                foreach (explode('/', $path) as $item) {
                    $path_item .= $item.'/';
                    if (!file_exists($path_item)) {
                        $result = mkdir($path_item);
                        // if (!$result) debug('path_item', $path_item);
                    } else {
                        $result = is_dir($path_item);
                    }
                    if (!$result) {
                        break;
                    }
                }
                $result &= is_writable($base_path.$path); // TODO: ??????
            }
        }
    }
    // debug('result', $result);
    return $result;
}

/**
 * check if the file is writable and if not, check that the directories leading to it do exist or can
 * be created
 * @param string $path the path, inclusive the file name, separated by /.
 * it must be a relative path starting from the current directory or from $base_path when defined.
 * @param string $path the part of the path where it should not create directories.
 */
function ensure_file_writable($path, $base_path = '') {
    $result = false;
    if ($base_path != '') {
        $base_path = rtrim($base_path, '/').'/';
        $path = trim(substr($path, count($base_path) - 1), '/');
    }
    if (file_exists($base_path.$path)) {
        $result = is_file($base_path.$path) && is_writable($base_path.$path);
    } else {
        $result = ensure_gitapi_directory_writable(dirname($path), $base_path);
    }
    return $result;
} // ensure_file_writable

function put_cache($path, $content, $manual_id = null) {
    $result = false;
    $path_cache = (isset($manual_id) ? $manual_id.'/' : '').$path;
    if (ensure_file_writable($path_cache, MANUAL_CACHE_PATH)) {
        file_put_contents(MANUAL_CACHE_PATH.$path_cache, $content);
    }
    return $result;
} // put_cache()

function get_cache($path, $content, $manual_id = null) {
    $result = false;
    $path_cache = (isset($manual_id) ? $manual_id.'/' : '').$path;
    if (ensure_file_writable($path_cache, MANUAL_CACHE_PATH)) {
        file_put_contents(MANUAL_CACHE_PATH.$path_cache, $content);
    }
    return $result;
} // put_cache()

function put_gitapi_cache($url, $content, $base_path = null) {
    if (is_null($base_path)) {
        $base_path = GITAPIGET_CACHE_PATH;
    }
    $file_name = $base_path.'/'.urlencode($url);
    // TODO: if file writable
    $result = file_put_contents($file_name, $content);
} // put_gitapi_cache()

function get_gitapi_cache($url, $base_path = null) {
    $result = '';
    if (is_null($base_path)) {
        $base_path = GITAPIGET_CACHE_PATH;
    }
    $file_name = $base_path.'/'.urlencode($url);
    if (file_exists($file_name)) {
        $result = file_get_contents($file_name);
    }
    return $result;
} // get_gitapi_cache()

function delete_gitapi_cache($url, $base_path = null) {
    if (is_null($base_path)) {
        $base_path = GITAPIGET_CACHE_PATH;
    }
    $file_name = $base_path.'/'.urlencode($url);
    // debug('file_name', $file_name);
    // TODO: if file writable
    $result = unlink($file_name);
} // delete_gitapi_cache()
