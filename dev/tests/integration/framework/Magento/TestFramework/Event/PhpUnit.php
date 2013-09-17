<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Listener of PHPUnit built-in events
 */
class Magento_TestFramework_Event_PhpUnit implements PHPUnit_Framework_TestListener
{
    /**
     * Used when PHPUnit framework instantiates the class on its own and passes nothing to the constructor
     *
     * @var Magento_TestFramework_EventManager
     */
    protected static $_defaultEventManager;

    /**
     * @var Magento_TestFramework_EventManager
     */
    protected $_eventManager;

    /**
     * Assign default event manager instance
     *
     * @param Magento_TestFramework_EventManager $eventManager
     */
    public static function setDefaultEventManager(Magento_TestFramework_EventManager $eventManager = null)
    {
        self::$_defaultEventManager = $eventManager;
    }

    /**
     * Constructor
     *
     * @param Magento_TestFramework_EventManager $eventManager
     * @throws Magento_Exception
     */
    public function __construct(Magento_TestFramework_EventManager $eventManager = null)
    {
        $this->_eventManager = $eventManager ?: self::$_defaultEventManager;
        if (!$this->_eventManager) {
            throw new Magento_Exception('Instance of the event manager is required.');
        }
    }

    /**
     * An error occurred.
     * Method is required by implemented interface, but is not needed by the class.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    /**
     * A failure occurred.
     * Method is required by implemented interface, but is not needed by the class.
     *
     * @param  PHPUnit_Framework_Test                 $test
     * @param  PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                  $time
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    /**
     * Incomplete test.
     * Method is required by implemented interface, but is not needed by the class.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    /**
     * Skipped test.
     * Method is required by implemented interface, but is not needed by the class.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     * @since  Method available since Release 3.0.0
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    /**
     * A test suite started.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        /* PHPUnit runs tests with data provider in own test suite for each test, so just skip such test suites */
        if ($suite instanceof PHPUnit_Framework_TestSuite_DataProvider) {
            return;
        }
        $this->_eventManager->fireEvent('startTestSuite');
    }

    /**
     * A test suite ended.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($suite instanceof PHPUnit_Framework_TestSuite_DataProvider) {
            return;
        }
        $this->_eventManager->fireEvent('endTestSuite', array($suite), true);
    }

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        if (!($test instanceof PHPUnit_Framework_TestCase) || ($test instanceof PHPUnit_Framework_Warning)) {
            return;
        }
        $this->_eventManager->fireEvent('startTest', array($test));
    }

    /**
     * A test ended.
     * Method signature is implied by implemented interface, not all parameters are needed.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if (!($test instanceof PHPUnit_Framework_TestCase) || ($test instanceof PHPUnit_Framework_Warning)) {
            return;
        }
        $this->_eventManager->fireEvent('endTest', array($test), true);
    }
}
