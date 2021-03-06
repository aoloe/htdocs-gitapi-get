<?php
/**
 * use an API compatible to the Github one to get the list of the files in the
 * git repository and, through their hashes, find out which ones are to be updated in
 * the local cache.
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

if (!defined('GITAPIGET_CACHE_LIB')) {
    include_once(dirname(__FILE__).'/cache.php');
}

if (!defined('GITAPIGET_GITAPI_LIB')) {
    include_once('gitapi.php');
}

define('GITAPIGET_LOCAL', true);

$rate_limit = json_decode(get_gitapi_raw(GITAPIGET_LOCAL ? get_gitapi_self_ratelimit_url() : get_gitapi_github_ratelimit_url()), true);
// debug('rate_limit', $rate_limit);

if ($rate_limit['resources']['core']['remaining'] == 0) {
    debug('request rate limit exceeded', $rate_limit['resources']['core']['remaining']);
    die();
} else {
    // TODO: correctly implemement the reset time
    echo("<p>You have ".$rate_limit['resources']['core']['remaining']." requests available until ".$rate_limit['resources']['core']['reset'].".</p>");
}

// get the list of the files on github, check their status against the cached list
// TODO: if force don't read the json cache
$cache = get_gitapi_cache('list.json', GITAPIGET_CACHE_PATH);
$cache = ($cache != '' ? json_decode($cache, true) : array());
// debug('list', $list);

$list = get_gitapi_list(
    (
        GITAPIGET_LOCAL ?
        get_gitapi_self_filelist_url('lg_projects') :
        get_gitapi_github_filelist_url('aoloe', 'libregraphics-projects')
    )
); 
$list = get_gitapi_compared_list($list, $cache);

// debug('list', $list);

// debug('GITAPIGET_CACHE_PATH', GITAPIGET_CACHE_PATH);

if (ensure_gitapi_directory_writable('content', GITAPIGET_CACHE_PATH)) {
    put_gitapi_cache('list.json', json_encode($list), GITAPIGET_CACHE_PATH);

    $action = update_gitapi_cache(
        $list,
        GITAPIGET_CACHE_PATH.'/content',
        GITAPIGET_LOCAL ? get_gitapi_self_raw_url() : get_gitapi_github_raw_url('aoloe', 'libregraphics-projects'),
        GITAPIGET_LOCAL
    );
    debug('action', $action);
}
