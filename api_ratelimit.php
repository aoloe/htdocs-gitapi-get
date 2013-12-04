<?php
/**
 * Output the rate limit for the requesting service (probably IP based). the answer format is compatible with the result of the Github API for the requests of the type:
 *
 * https://api.github.com/rate_limit
 *
 * The request is of the type:
 *
 * http://test.com/api_ratelimit.php
 *
 * The result is of type text/json
 *
 * - currently it always returns 60, the maximal hourly rate on github
 * - it only returns the "resources > core" part, ignoring for now the deprecated part and other
 *   resources.
 * - the reset option is not set.
 */

function debug($label, $value) {
    echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
}

$result = array (
    'resources' => array (
        'core' => array (
            'limit' => 60,
            'remaining' => 60,
            'reset' => 0, // the time when the current rate limit resets in seconds (?)
        ),
    ),
);

echo(json_encode($result));
