<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

if (!defined('GITAPIGET_CACHE_PATH')) {
    define('GITAPIGET_CACHE_PATH', 'cache/');
}

function get_github_filelist_url($user, $repository) {
    return strtr(
        'https://api.github.com/repos/$user/$repository/git/trees/master?recursive=1',
        array(
            '$user' => $user,
            '$repository' => $repository,
        )
    );
}

function get_github_raw_url($user, $repository) {
    return strtr(
        'https://raw.github.com/$user/$repository/master/',
        array(
            '$user' => $user,
            '$repository' => $repository,
        )
    );
}
