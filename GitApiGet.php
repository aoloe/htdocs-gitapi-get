<?php
/**
 * Object oriented proxy to the gitapi library to be used in Slim
 */

require_once dirname(__FILE__).'/gitapi.php'

class GitApiGet {
    public function get_github_ratelimit_url() {
        get_gitapi_github_ratelimit_url();
    }

    public function get_github_filelist_url($user, $repository, $branch = null) {
        get_gitapi_github_filelist_url($user, $repository, $branch);
    }

    public function get_github_raw_url($user, $repository, $branchl) {
        get_gitapi_github_raw_url($user, $repository, $branch);
    }

    public function get_self_ratelimit_url($hash = null) {
        get_gitapi_self_ratelimit_url($hash);
    }

    public function get_self_filelist_url($hash) {
        get_gitapi_self_filelist_url($hash);
    }

    public function get_self_raw_url($hash = null) {
        get_gitapi_self_raw_url($hash);
    }

    public function get_list($url, $cache_id, $cache) {
        get_gitapi_list($url, $cache_id, $cache);
    }

    public function get_tree($url, $cache_id, $cache = null) {
        get_gitapi_tree($url, $cache_id, $cache);
    }

    public function get_raw($url) {
        get_gitapi_raw($url);
    }

    public function ensure_cache($path_cache) {
        ensure_gitapi_cache($path_cache);
    }
    public function update_cache($list, $path_cache, $url_raw, $use_get) {
        update_gitapi_cache($list, $path_cache, $url_raw, $use_get);
    }
}
