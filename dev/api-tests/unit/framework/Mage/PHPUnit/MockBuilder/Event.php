<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Event mock object creator for Mage::dispatchEvent(...) construction.
 * getMock() returns mock object of Mage_PHPUnit_Fixture_Event class and then 'run' method
 * can be overrided using callback, for example, to check the arguments
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_MockBuilder_Event extends Mage_PHPUnit_MockBuilder_Abstract
{
    /**
     * Main stub class name which will be mocked.
     *
     * @var string
     */
    protected $_eventStubClass = 'Mage_PHPUnit_Stub_Event';

    /**
     * Event name
     *
     * @var string
     */
    protected $_eventName;

    /**
     * 'Run' method name.
     *
     * @var string
     */
    protected $_runMethod = 'run';

    /**
     * Constructor
     *
     * @param PHPUnit_Framework_TestCase $testCase
     * @param string $eventName
     */
    public function __construct(PHPUnit_Framework_TestCase $testCase, $eventName)
    {
        $this->testCase  = $testCase;
        $this->_eventName = $eventName;
    }

    /**
     * Returns event's name. Only getter.
     * For another event please create another builder.
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->_eventName;
    }

    /**
     * Gets run method name
     *
     * @return string
     */
    public function getRunMethod()
    {
        return $this->_runMethod;
    }

    /**
     * Sets run method name
     *
     * @param string $methodName
     * @return Mage_PHPUnit_MockBuilder_Event
     */
    public function setRunMethod($methodName)
    {
        $this->_runMethod = $methodName;
        return $this;
    }

    /**
     * Returns PHPUnit event's helper.
     *
     * @return Mage_PHPUnit_Helper_Event
     */
    protected function _getEventHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('event');
    }

    /**
     * Returns PHPUnit singleton helper.
     *
     * @return Mage_PHPUnit_Helper_Singleton
     */
    protected function _getSingletonHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('singleton');
    }

    /**
     * Method which is called before getMock() method.
     */
    protected function _prepareMock()
    {
        $this->className = $this->_eventStubClass;
    }

    /**
     * Method which is called after getMock() method.
     *
     * @param PHPUnit_Framework_MockObject_MockObject|object $mock
     */
    protected function _afterGetMock($mock)
    {
        $this->_removeEventDataFromConfig();
        $this->_registerMockEvent($mock);
    }

    /**
     * Removes all data for event from config.
     */
    protected function _removeEventDataFromConfig()
    {
        $this->_getEventHelper()->disableObservers(array($this->getEvent()));
        return $this;
    }

    /**
     * Register own observer object to the event with 'run' method.
     *
     * @param PHPUnit_Framework_MockObject_MockObject|object $mock
     */
    protected function _registerMockEvent($mock)
    {
        //set Singleton
        $modelKey = '___mock___event_' . $this->getEvent();
        $registryKey = '_singleton/' . $modelKey;

        $this->_getSingletonHelper()->registerSingleton($registryKey, $mock);

        //add observer to event
        $this->_getEventHelper()->addObserverToEvent(
            $this->getEvent(),
            "observer_{$modelKey}",
            $modelKey,
            $this->getRunMethod()
        );
    }
}
