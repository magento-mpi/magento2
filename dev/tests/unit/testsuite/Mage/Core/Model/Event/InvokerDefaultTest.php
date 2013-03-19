<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Event_Invoker_InvokerDefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $arguments
     * @return Mage_Core_Model_Event_InvokerDefault
     */
    protected function _getEventInvokerDefault($arguments)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        return $objectManagerHelper->getObject('Mage_Core_Model_Event_InvokerDefault', $arguments);
    }

    public function testDispatchWithDisabledType()
    {
        $observerFactoryMock = $this->getMock('Mage_Core_Model_ObserverFactory', array(), array(), '', false);
        $observerFactoryMock->expects($this->never())->method('get');
        $observerFactoryMock->expects($this->never())->method('create');
        $configuration = array('type' => 'disabled');
        $observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $defaultInvoker = $this->_getEventInvokerDefault(array(
            'observerFactory' => $observerFactoryMock,
        ));
        $defaultInvoker->dispatch($configuration, $observerMock);
    }

    /**
     * @param string $type
     * @dataProvider dataProviderForDispatchWithNotSingletonType
     */
    public function testDispatchWithNotSingletonType($type)
    {
        $objectMock = $this->getMock('Mage_Some_Model_Observer_Some', array('method_name'), array(), '', false);
        $objectMock->expects($this->once())
            ->method('method_name');

        $observerFactoryMock = $this->getMock('Mage_Core_Model_ObserverFactory', array(), array(), '', false);
        $observerFactoryMock->expects($this->never())->method('get');
        $observerFactoryMock->expects($this->once())->method('create')->with('class_name')
            ->will($this->returnValue($objectMock));

        $configuration = array('type' => $type, 'model' => 'class_name', 'method' => 'method_name');
        $observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $defaultInvoker = $this->_getEventInvokerDefault(array(
            'observerFactory' => $observerFactoryMock,
        ));
        $defaultInvoker->dispatch($configuration, $observerMock);
    }

    /**
     * @return array
     */
    public function dataProviderForDispatchWithNotSingletonType()
    {
        return array(
            array('object'),
            array('model'),
        );
    }

    public function testDispatchWithSingletonType()
    {
        $objectMock = $this->getMock('Mage_Some_Model_Observer_Some', array('method_name'), array(), '', false);
        $objectMock->expects($this->once())
            ->method('method_name');

        $observerFactoryMock = $this->getMock('Mage_Core_Model_ObserverFactory', array(), array(), '', false);
        $observerFactoryMock->expects($this->never())->method('create');
        $observerFactoryMock->expects($this->once())->method('get')->with('class_name')
            ->will($this->returnValue($objectMock));

        $configuration = array('type' => 'unknown', 'model' => 'class_name', 'method' => 'method_name');
        $observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $defaultInvoker = $this->_getEventInvokerDefault(array(
            'observerFactory' => $observerFactoryMock,
        ));
        $defaultInvoker->dispatch($configuration, $observerMock);
    }

    /**
     * @param string $type
     * @dataProvider dataProviderForMethodIsNotDefined
     * @expectedException Mage_Core_Exception
     */
    public function testMethodIsNotDefinedExceptionWithEnabledDeveloperMode($type)
    {
        $objectMock = $this->getMock('Mage_Some_Model_Observer_Some', array(), array(), '', false);

        $observerFactoryMock = $this->getMock('Mage_Core_Model_ObserverFactory', array(), array(), '', false);
        $observerFactoryMock->expects($this->any())->method('create')->with('class_name')
            ->will($this->returnValue($objectMock));
        $observerFactoryMock->expects($this->any())->method('get')->with('class_name')
            ->will($this->returnValue($objectMock));

        $appStateMock = $this->getMock('Mage_Core_Model_App_State', array(), array(), '', false);
        $appStateMock->expects($this->once())
            ->method('isDeveloperMode')
            ->will($this->returnValue(true));

        $configuration = array('type' => $type, 'model' => 'class_name', 'method' => 'unknown_method_name');
        $observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $defaultInvoker = $this->_getEventInvokerDefault(array(
            'observerFactory' => $observerFactoryMock,
            'appState' => $appStateMock,
        ));
        $defaultInvoker->dispatch($configuration, $observerMock);
    }

    /**
     * @param string $type
     * @dataProvider dataProviderForMethodIsNotDefined
     */
    public function testMethodIsNotDefinedWithDisabledDeveloperMode($type)
    {
        $objectMock = $this->getMock('Mage_Some_Model_Observer_Some', array(), array(), '', false);

        $observerFactoryMock = $this->getMock('Mage_Core_Model_ObserverFactory', array(), array(), '', false);
        $observerFactoryMock->expects($this->any())->method('create')->with('class_name')
            ->will($this->returnValue($objectMock));
        $observerFactoryMock->expects($this->any())->method('get')->with('class_name')
            ->will($this->returnValue($objectMock));

        $appStateMock = $this->getMock('Mage_Core_Model_App_State', array(), array(), '', false);
        $appStateMock->expects($this->once())->method('isDeveloperMode')->will($this->returnValue(false));

        $configuration = array('type' => $type, 'model' => 'class_name', 'method' => 'unknown_method_name');
        $observerMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $defaultInvoker = $this->_getEventInvokerDefault(array(
            'observerFactory' => $observerFactoryMock,
            'appState' => $appStateMock,
        ));
        $defaultInvoker->dispatch($configuration, $observerMock);
    }

    /**
     * @return array
     */
    public function dataProviderForMethodIsNotDefined()
    {
        return array(
            array('object'),
            array('model'),
            array('unknown'),
        );
    }
}
