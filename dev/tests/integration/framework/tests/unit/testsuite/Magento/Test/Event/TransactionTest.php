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
 * Test class for Magento_Test_Event_Transaction.
 */
class Magento_Test_Event_TransactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Event_Transaction|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var Magento_Test_EventManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManager;

    /**
     * @var Magento_Test_Db_Adapter_TransactionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapter;

    protected function setUp()
    {
        $this->_eventManager = $this->getMock('Magento_Test_EventManager', array('fireEvent'), array(array()));
        $this->_adapter = $this->getMock('Magento_Test_Db_Adapter_TransactionInterface', array(
            'beginTransparentTransaction',
            'commitTransparentTransaction',
            'rollbackTransparentTransaction',
        ));
        $this->_object = $this->getMock(
            'Magento_Test_Event_Transaction', array('_getAdapter'), array($this->_eventManager)
        );
        $this->_object
            ->expects($this->any())
            ->method('_getAdapter')
            ->will($this->returnValue($this->_adapter))
        ;
    }

    /**
     * Imitate transaction start request
     *
     * @param string $eventName
     */
    protected function _requestBeginTransaction($eventName)
    {
        $callback = function ($eventName, array $parameters) {
            /** @var $param Magento_Test_Event_Param_Transaction */
            $param = $parameters[1];
            $param->requestTransactionBegin();
        };
        $this->_eventManager
            ->expects($this->at(0))
            ->method('fireEvent')
            ->with($eventName)
            ->will($this->returnCallback($callback))
        ;
    }

    /**
     * Setup expectations for "begin transaction" use case
     *
     * @param PHPUnit_Framework_MockObject_Matcher_Invocation $invocationMatcher
     */
    protected function _expectBeginTransaction(PHPUnit_Framework_MockObject_Matcher_Invocation $invocationMatcher)
    {
        $this->_eventManager
            ->expects($invocationMatcher)
            ->method('fireEvent')
            ->with('beginTransaction')
        ;
        $this->_adapter
            ->expects($this->once())
            ->method('beginTransparentTransaction')
        ;
    }

    /**
     * Imitate transaction rollback request
     *
     * @param string $eventName
     */
    protected function _requestRollbackTransaction($eventName)
    {
        $callback = function ($eventName, array $parameters) {
            /** @var $param Magento_Test_Event_Param_Transaction */
            $param = $parameters[1];
            $param->requestTransactionRollback();
        };
        $this->_eventManager
            ->expects($this->at(0))
            ->method('fireEvent')
            ->with($eventName)
            ->will($this->returnCallback($callback))
        ;
    }

    /**
     * Setup expectations for "rollback transaction" use case
     *
     * @param PHPUnit_Framework_MockObject_Matcher_Invocation $invocationMatcher
     */
    protected function _expectRollbackTransaction(PHPUnit_Framework_MockObject_Matcher_Invocation $invocationMatcher)
    {
        $this->_eventManager
            ->expects($invocationMatcher)
            ->method('fireEvent')
            ->with('rollbackTransaction')
        ;
        $this->_adapter
            ->expects($this->once())
            ->method('rollbackTransparentTransaction')
        ;
    }

    /**
     * @param string $method
     * @param string $eventName
     * @dataProvider beginAndRollbackTransactionDataProvider
     */
    public function testBeginAndRollbackTransaction($method, $eventName)
    {
        $this->_requestBeginTransaction($eventName);
        $this->_expectBeginTransaction($this->at(1));
        $this->_object->$method($this);

        $this->_requestRollbackTransaction($eventName);
        $this->_expectRollbackTransaction($this->at(1));
        $this->_object->$method($this);
    }

    public function beginAndRollbackTransactionDataProvider()
    {
        return array(
            'method "startTest"' => array('startTest', 'startTestTransactionRequest'),
            'method "endTest"'   => array('endTest',   'endTestTransactionRequest'),
        );
    }

    public function testEndTestSuiteDoNothing()
    {
        $this->_eventManager
            ->expects($this->never())
            ->method('fireEvent')
        ;
        $this->_adapter
            ->expects($this->never())
            ->method($this->anything())
        ;
        $this->_object->endTestSuite();
    }

    public function testEndTestSuiteRollbackTransaction()
    {
        $this->_requestBeginTransaction('startTestTransactionRequest');
        $this->_object->startTest($this);

        $this->_expectRollbackTransaction($this->once());
        $this->_object->endTestSuite();
    }
}
