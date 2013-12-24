# gitapi-get

Get files from a Git repository implementing an API compatible with the one provided by Github.

It keeps a local cache in sync with a Git repository by using a public API and downloading the files through HTTP with curl.

# Install

If you want create a local cache of a repository create the `update.php` file:

    <?php
    define('GITAPIGET_CACHE_PATH', dirname(__FILE__).'/cache');
    include('engine/update.php');

If you want to serve a list of files in a local directory create the `api_filelist.php` file

    <?php
    include('engine/api_filelist.php');
    define('GITAPIGET_FILELIST_CONFIG_FILE', dirname(__FILE__).'/config.json');
    render_gitapiget_filelist(get_gitapiget_filelist_path());

If you want to serve files from a local directory create the `api_fileraw.php` file

    <?php
    define('GITAPIGET_CACHE_PATH', dirname(__FILE__).'/cache');
    define('GITAPIGET_API_FILERAW_PATH', '/home/ale/docs/src/libregraphics-projects/');
    // define('GITAPIGET_API_FILERAW_URLENCODE_PATH', true);
    include('engine/api_fileraw.php');

If you want to have a counter for the hits coming from a specific IP address create the `api_ratelimit.php` file

    <?php
    include('engine/api_ratelimit.php');

# TODO

- integrate the simplejson and mycurl for servers that don't have them enabled

# Further inspiration

- [The Github API](http://developer.github.com/v3)
- [The Github development guides](https://developer.github.com/guides/)
- [A simple Object Oriented wrapper for GitHub API, written with PHP5](https://github.com/KnpLabs/php-github-api): implements fetching and setting lot of informations for the Github repositories (but no handling of files).
- [GitHub API PHP Client](https://github.com/tan-tan-kanarek/github-php-client): implements lists of commits.
