<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Listener_Observers
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Listener_Observers_TeamcityObserver extends PHPUnit_Util_Printer
{
    /**
     * @var Mage_Listener_EventListener
     */
    protected $_listener;

    /**
     * @var bool
     */
    protected $_failed;

    /**
     * @var string
     */
    protected $_lastTest;

    /**
     * Counter of errors
     *
     * @var int
     */
    protected $_errorsCount = 0;

    /**
     * Counter of failures
     *
     * @var int
     */
    protected $_failuresCount = 0;

    /**
     * Constructs Teamcity Observer
     *
     * @param Mage_Listener_EventListener $listener
     */
    public function __construct(Mage_Listener_EventListener $listener)
    {
        $this->setListener($listener);
    }

    /**
     * Sets protected variable $listener
     *
     * @param Mage_Listener_EventListener $listener
     *
     * @return Mage_Listener_Observers_TeamcityObserver
     */
    protected function setListener($listener)
    {
        $this->_listener = $listener;
        return $this;
    }

    /**
     * Gets protected variable $listener
     *
     * @return Mage_Listener_EventListener|null
     */
    protected function getListener()
    {
        return $this->_listener;
    }

    /**
     * A test started.
     *
     */
    public function startTest()
    {
        $listener = $this->getListener();
        $test = ($listener) ? $listener->getCurrentTest(): null;
        if ($test != null) {
            if ($this->_lastTest != $test->getName(false)) {
                $this->_failed = false;
            }
            $this->_lastTest = $test->getName(false);
        }
        $message = sprintf("##teamcity[testStarted name='%s' captureStandardOutput='%s']" . PHP_EOL,
            $test->getName(),
            'true'
        );
        $this->write($message);
    }

    /**
     * Generates message from last failures and errors
     *
     * @return string
     */
    protected function getMessage()
    {
        $listener = $this->getListener();
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $message = '';
        if ($test != null) {
            $message = get_class($test) . ":" . $test->getName() . "\n";
            $errorsCount = $test->getTestResultObject()->errorCount();
            $failuresCount = $test->getTestResultObject()->failureCount();
            if ($errorsCount > $this->_errorsCount) {
                $errors = $test->getTestResultObject()->errors();
                $error = end($errors);
                $message .= $error->exceptionMessage();
                $this->_errorsCount = $errorsCount;
            }
            if ($failuresCount > $this->_failuresCount) {
                $fails = $test->getTestResultObject()->failures();
                $fail = end($fails);
                $message .= $fail->exceptionMessage();
                $this->_failuresCount = $failuresCount;
            }
        }
        return $message;
    }

    /**
     * Test Failed
     */
    public function testFailed()
    {
        $this->_failed = true;
        $listener = $this->getListener();
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $e = $this->getMessage();
        $message = sprintf("##teamcity[testFailed name='%s' message='%s']" . PHP_EOL, $test->getName(), $e);
        $this->write($message);
    }

    /**
     * Handler for 'testSkipped' event
     */
    public function testSkipped()
    {
        $this->_failed = true;
        $listener = $this->getListener();
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $e = $this->getMessage();
        $message = sprintf("##teamcity[testIgnored name='%s' message='%s']" . PHP_EOL, $test->getName(), $e);
        $this->write($message);
    }

    /**
     * Handler for 'testIncomplete' event
     */
    public function testIncomplete()
    {
        $this->_failed = true;
        $listener = $this->getListener();
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $e = $this->getMessage();
        $message = sprintf("##teamcity[testIgnored name='%s' message='%s']" . PHP_EOL, $test->getName(), $e);
        $this->write($message);
    }

    /**
     * Handler for 'endTest' event
     */
    public function endTest()
    {
        $listener = $this->getListener();
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $message = sprintf("##teamcity[testPassed name='%s']" . PHP_EOL, $test->getName());
        $this->write($message);
    }
}