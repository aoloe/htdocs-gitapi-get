# gitapi-get

Get files from a Git repository implementing an API compatible with the one provided by Github.

It keeps a local cache in sync with a Git repository by using a public API and downloading the files through HTTP with curl.

# Install

    <?php
    define('GITAPIGET_CACHE_PATH', dirname(__FILE__).'/cache');
    include('engine/update.php');


    <?php
    include('engine/api_filelist.php');
    define('GITAPIGET_FILELIST_CONFIG_FILE', dirname(__FILE__).'/config.json');
    render_gitapiget_filelist(get_gitapiget_filelist_path());


    <?php
    define('GITAPIGET_CACHE_PATH', dirname(__FILE__).'/cache');
    define('GITAPIGET_API_FILERAW_PATH', '/home/ale/docs/src/libregraphics-projects/');
    // define('GITAPIGET_API_FILERAW_URLENCODE_PATH', true);
    include('engine/api_fileraw.php');


    <?php
    include('engine/api_ratelimit.php');


# Configure


# TODO

- integrate the simplejson and mycurl for servers that don't have them enabled
