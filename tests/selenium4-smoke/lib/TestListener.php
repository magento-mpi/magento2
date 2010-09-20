<?php

require_once 'PHPUnit/Framework.php';

class TestListener implements PHPUnit_Framework_TestListener
{

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        printf("\nError while running test <%s>.", $test->getName());
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        printf("\nTest <%s> failed.", $test->getName());
    }

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        printf("\nTest <%s> is incomplete.", $test->getName());
    }

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        printf("\nTest <%s> has been skipped.", $test->getName());
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        printf("\nTest <%s> started.", $test->getName());
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        printf("\nTest <%s> ended.", $test->getName());
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        printf("\nTestSuite <%s> started.", $suite->getName() ? $suite->getName() : 'MAIN');
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        printf("\nTestSuite <%s> ended.", $suite->getName() ? $suite->getName() : 'MAIN');
    }

}
