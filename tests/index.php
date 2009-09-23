<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../vendors/');

require_once 'PHPUnit2/Util/CodeCoverage/Renderer.php';
require_once 'simpletest/unit_tester.php';
require_once 'simpletest/mock_objects.php';
require_once 'simpletest/reporter.php';
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

//xdebug_start_code_coverage(XDEBUG_CC_UNUSED);

$test->run(new TextReporter());

/*
$coverage = xdebug_get_code_coverage();
foreach ($coverage as $filename => $c) {
    if (strpos($filename, '/models/') === false) continue;
    $count = array_count_values($c);
    $lines_missed = array_key_exists(-1, $count) ? $count[-1] : 0;
    $lines_hit    = array_key_exists(1,  $count) ? $count[1]  : 0;
    $total = $lines_missed + $lines_hit;
    $percentage = sprintf("%6s", sprintf("%01.2f", ($lines_hit / $total) * 100));
    print "$percentage% => $filename\n";
}
$cc =  PHPUnit2_Util_CodeCoverage_Renderer::factory('HTML',array('tests' => $coverage));
$cc->renderToFile('cov.html');
*/

?>
