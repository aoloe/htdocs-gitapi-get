<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once('treepath.php');
require_once('api_filelist.php');
require_once('cache.php');
require_once('fileraw.php');
require_once('gitapi.php');

class AllTests extends TestSuite {
    function __construct() {
        parent::__construct();
        // $this->TestSuite('All tests');
        // $this->add(new TestOfSetTreePath());
        // $this->add(new TestOfAddTreePath());
    }
}
