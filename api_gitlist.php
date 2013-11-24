<?php

/**
 * Output a directoy listing compatible with the result of the Github API for the requests of the type:
 *
 * https://api.github.com/repos/$user/$repository/git/trees/master?recursive=1
 *
 * The request is of the type:
 *
 * http://test.com/api_gitlist.php?hash=abcd
 *
 * The result is of type text/json
 *
 * - This API allows you to use the update.php script with git repositories that are not on Github.
 * - This version queries a local git install.
 * - The hash is a 40 hex digit sha1 hash built as sha1("blob " + filesize + "\0" + data).
 * - The available repositores are defined in the config.json file and can be queried with the hash parameter
 *   (GET or POST).
 */

// TODO: implement this as soon as it is needed.
