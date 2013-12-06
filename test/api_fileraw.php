<?php
require_once(dirname(dirname(__FILE__)) . '/api_fileraw.php');

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<p>$label<br /><pre>".htmlentities(print_r($value, 1))."</pre></p>");
    }
}

class TestOfAPIFileraw extends UnitTestCase {
    function testGetFilerawText() {
        $expected = 'this is a test text.';
        $filename = dirname(__FILE__).'/api_fileraw/text/text.txt';
        $result = trim(get_gitapi_fileraw($filename));
        // debug('result', $result);
        // debug('expected', $expected);
        $this->assertIdentical($result, $expected);
    }
}
