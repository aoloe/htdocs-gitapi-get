<?php

function get_gitapi_array_count_increment($value) {
    return (
        !empty($value) &&
        (
            (
                !array_key_exists('type', $value) ||
                ($value['type'] != 'tree')
            ) &&
            (
                !array_key_exists('status', $value) ||
                ($value['status'] != 'delete')
            )
        )
    ) ? 1 : 0;
}

function set_gitapi_array_path(&$arr, $path, $value)
{
    if (empty($arr)) {
        $arr = array (
            'count' => 0,
            'tree' => array(),
        );
    }
    $segment = is_array($path) ? $path : explode('/', $path);
    // debug('segment', $segment);
    if ($path == '') {
        $arr = $arr + $value;
        $arr['count'] += get_gitapi_array_count_increment($value);
    } elseif (count($segment) == 1) {
        $arr['tree'][end($segment)] = $value;
        $arr['count'] += get_gitapi_array_count_increment($value);
    } else {
        $count = get_gitapi_array_count_increment($value);
        // debug('count', $count);
        $arr['count'] += $count;
        $cur =& $arr['tree'];
        foreach (array_slice($segment, 0, -1) as $item) {
            if (!isset($cur[$item])) {
                $cur[$item] = array();
            }
            if (!array_key_exists('count', $cur[$item])) {
                $cur[$item]['count'] = 0;
                $cur[$item]['tree'] = array();
            }
            $cur[$item]['count'] += $count;
            $cur =& $cur[$item]['tree'];
        }
        $cur[end($segment)] = $value;
    }
} //  set_gitapi_array_path()

function get_gitapi_array_path($arr, $path)
{
    if ($path == '')
        return null;

    $segment = is_array($path) ? $path : explode('/', $path);
    $cur =& $arr;
    foreach ($segment as $item) {
        if (!isset($cur[$item]))
            return null;

        $cur = $cur[$segment];
    }
    return $cur;
} // get_gitapi_array_path()

/*
 * inspired by Venkat D. answer on http://stackoverflow.com/a/9706755

function array_get($arr, $path)
{
    if (!$path)
        return null;

    $segments = is_array($path) ? $path : explode('/', $path);
    $cur =& $arr;
    foreach ($segments as $segment) {
        if (!isset($cur[$segment]))
            return null;

        $cur = $cur[$segment];
    }

    return $cur;
}

function array_set(&$arr, $path, $value)
{
    if (!$path)
        return null;

    $segments = is_array($path) ? $path : explode('/', $path);
    $cur =& $arr;
    foreach ($segments as $segment) {
        if (!isset($cur[$segment]))
            $cur[$segment] = array();
        $cur =& $cur[$segment];
    }
    $cur = $value;
}
*/
