<?php
require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once('treepath.php');

class AllTests extends TestSuite {
    function __construct() {
        parent::__construct();
        // $this->add(new TestOfSetTreePath());
        // $this->add(new TestOfAddTreePath());
    }
}
