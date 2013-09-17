<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GoogleAdwords_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventMock;

    /**
     * @var Magento_GoogleAdwords_Model_Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_helperMock = $this->getMock('Magento_GoogleAdwords_Helper_Data', array(), array(), '', false);
        $this->_registryMock = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', true);
        $this->_collectionMock = $this->getMock('Magento_Sales_Model_Resource_Order_Collection', array(), array(), '',
            false);
        $this->_eventObserverMock = $this->getMock('Magento_Event_Observer', array(), array(), '', false);
        $this->_eventMock = $this->getMock('Magento_Event', array('getOrderIds'), array(), '', false);

        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Magento_GoogleAdwords_Model_Observer', array(
            'helper' => $this->_helperMock,
            'collection' => $this->_collectionMock,
            'registry' => $this->_registryMock
        ));
    }

    public function dataProviderForDisabled()
    {
        return array(
            array(false, false),
            array(false, true),
            array(true, false),
        );
    }

    /**
     * @param bool $isActive
     * @param bool $isDynamic
     * @dataProvider dataProviderForDisabled
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSetConversionValueWhenAdwordsDisabled($isActive, $isDynamic)
    {
        $this->_helperMock->expects($this->once())->method('isGoogleAdwordsActive')
            ->will($this->returnValue($isActive));
        $this->_helperMock->expects($this->any())->method('isDynamicConversionValue')
            ->will($this->returnCallback(
                function () use ($isDynamic) {
                    return $isDynamic;
                }
            ));

        $this->_eventMock->expects($this->never())->method('getOrderIds');
        $this->assertSame($this->_model, $this->_model->setConversionValue($this->_eventObserverMock));
    }

    public function dataProviderForOrdersIds()
    {
        return array(
            array(array()),
            array(''),
        );
    }

    /**
     * @param $ordersIds
     * @dataProvider dataProviderForOrdersIds
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSetConversionValueWhenAdwordsActiveWithoutOrdersIds($ordersIds)
    {
        $this->_helperMock->expects($this->once())->method('isGoogleAdwordsActive')->will($this->returnValue(true));
        $this->_helperMock->expects($this->once())->method('isDynamicConversionValue')->will($this->returnValue(true));
        $this->_eventMock->expects($this->once())->method('getOrderIds')->will($this->returnValue($ordersIds));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')
            ->will($this->returnValue($this->_eventMock));
        $this->_collectionMock->expects($this->never())->method('addFieldToFilter');

        $this->assertSame($this->_model, $this->_model->setConversionValue($this->_eventObserverMock));
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSetConversionValueWhenAdwordsActiveWithOrdersIds()
    {
        $ordersIds = array(1, 2, 3);
        $conversionValue = 0;
        $this->_helperMock->expects($this->once())->method('isGoogleAdwordsActive')->will($this->returnValue(true));
        $this->_helperMock->expects($this->once())->method('isDynamicConversionValue')->will($this->returnValue(true));
        $this->_eventMock->expects($this->once())->method('getOrderIds')->will($this->returnValue($ordersIds));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')
            ->will($this->returnValue($this->_eventMock));

        $iteratorMock = $this->getMock('Iterator');
        $this->_collectionMock->expects($this->any())->method('getIterator')->will($this->returnValue($iteratorMock));
        $this->_collectionMock->expects($this->once())->method('addFieldToFilter')
            ->with('entity_id', array('in' => $ordersIds));
        $this->_registryMock->expects($this->once())->method('register')
            ->with(Magento_GoogleAdwords_Helper_Data::CONVERSION_VALUE_REGISTRY_NAME, $conversionValue);

        $this->assertSame($this->_model, $this->_model->setConversionValue($this->_eventObserverMock));
    }
}
