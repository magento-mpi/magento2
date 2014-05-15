<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Centralized entry point for handling PHPUnit built-in and custom events for Testlink integration
 */
class Mage_Testlink_Listener implements PHPUnit_Framework_TestListener
{

    /**
     * Registered event observers classes
     *
     * @var array
     */
    protected static $_observerClasses = array();

    /**
     * Registered event observers
     *
     * @var array
     */
    protected $_observers = array();

    /**
     * @var PHPUnit_Framework_TestCase
     */
    protected $_currentTest;

    /**
     * @var string|null
     */
    protected $_project;

    /**
     * @var string|null
     */
    protected $_testPlan;

    /**
     * @var string|null
     */
    protected $_build;

    /**
     * Gets the project
     *
     * @return string|null
     */
    public function getProject()
    {
        return $this->_project;
    }

    /**
     * Gets test plan
     *
     * @return string|null
     */
    public function getTestPlan()
    {
        return $this->_testPlan;
    }

    /**
     * Gets build
     *
     * @return string|null
     */
    public function getBuild()
    {
        return $this->_build;
    }

    /**
     * Retrieve currently running test
     *
     * @return PHPUnit_Framework_TestCase
     */
    public function getCurrentTest()
    {
        return $this->_currentTest;
    }

    /**
     * Puts observer class to array
     *
     * @static
     * @param $observerClass
     */
    public static function registerObserver($observerClass)
    {
        self::$_observerClasses[] = $observerClass;
    }

    /**
     * Initializes connection settings
     */
    public function __construct($project=null, $url=null, $devkey=null, $testPlan=null, $build=null)
    {
        //Initialize Testlink credentials
        if (isset($url)) {
            Mage_Testlink_Connector::$serverURL = $url;
        }
        Mage_Testlink_Connector::$devKey = $devkey;
        $this->_project = $project;
        $this->_testPlan = $testPlan;
        $this->_build = $build;
        if (isset($devkey) && isset($project)
                && ($devkey != "null") && ($project != "null")
                && ($devkey != "false") && ($project != "false")) {
            $this->instantiateObservers();
        }
    }

    /**
     * Constructor instantiates observers from registered classes and passes itself to constructor
     */
    protected function instantiateObservers()
    {
        foreach (self::$_observerClasses as $observerClass) {
            $this->_observers[] = new $observerClass($this);
        }
    }

    /**
     * Notify registered observers that are interested in the event
     *
     * @param string $eventName
     * @param bool $reverseOrder
     */
    protected function notifyObservers($eventName, $reverseOrder = false)
    {
        $observers = ($reverseOrder ? array_reverse($this->_observers) : $this->_observers);
        foreach ($observers as $observerInstance) {
            $callback = array($observerInstance, $eventName);
            if (is_callable($callback)) {
                call_user_func($callback);
            }
        }
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->_currentTest = $test;
        $this->notifyObservers('testFailed');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->_currentTest = $test;
        $this->notifyObservers('testFailed');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->_currentTest = $test;
        $this->notifyObservers('testIncomplete');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->_currentTest = $test;
        $this->notifyObservers('testRisky');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->_currentTest = $test;
        $this->notifyObservers('testSkipped');
    }

    /**
     * {@inheritdoc}
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        /* PHPUnit runs tests with data provider in own test suite for each test, so just skip such test suites */
        if ($suite instanceof PHPUnit_Framework_TestSuite_DataProvider) {
            return;
        }
        $this->notifyObservers('startTestSuite');
    }

    /**
     * {@inheritdoc}
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($suite instanceof PHPUnit_Framework_TestSuite_DataProvider) {
            return;
        }
        $this->notifyObservers('endTestSuite', true);
    }

    /**
     * {@inheritdoc}
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        if (!($test instanceof PHPUnit_Framework_TestCase) || ($test instanceof PHPUnit_Framework_Warning)) {
            return;
        }
        $this->_currentTest = $test;
        $this->notifyObservers('startTest');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if (!($test instanceof PHPUnit_Framework_TestCase) || ($test instanceof PHPUnit_Framework_Warning)) {
            return;
        }
        $this->notifyObservers('endTest', true);
    }
}
