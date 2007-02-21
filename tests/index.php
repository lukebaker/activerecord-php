<?php

require_once '../vendors/simpletest/unit_tester.php';
require_once '../vendors/simpletest/mock_objects.php';
require_once '../vendors/simpletest/reporter.php';
require_once 'BaseTest.php';

$files = glob(dirname(__FILE__) . '/*_test.php');
if (!isset($argv[1])) {
  $test =& new GroupTest('Running All Tests ...');
  foreach ($files as $file) {
    $test->addTestFile($file);
  }
}
else {
  $test =& new GroupTest("Running Tests For {$argv[1]} ...");
  $test->addTestFile(dirname(__FILE__) . '/' . $argv[1]);
}

$test->run(new TextReporter());


?>
