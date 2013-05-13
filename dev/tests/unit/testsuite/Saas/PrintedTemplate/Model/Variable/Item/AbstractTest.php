<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage unit_tests
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Saas_PrintedTemplate_Model_Variable_Item_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testGetOrderItem()
    {
        $orderItem = new Varien_Object();
        $valueModel = new Varien_Object();
        $valueModel->setOrderItem($orderItem);

        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Item_FakeAbstract')
            ->disableOriginalConstructor()
            ->setMethods(array('_setListsFromConfig'))
            ->getMock();

        $entity->__construct($valueModel);
        $actualOrderItem = $entity->getOrderItem();

        $this->assertEquals($orderItem, $actualOrderItem);
    }

    /**
     * Test get children method
     *
     * @param int orderItemId
     * @param array $itemsData
     * @param array $expectedOrderItemIds
     * @dataProvider getChildrenProvider
     */
    public function testGetChildren($orderItemId, $itemsData, $expectedOrderItemIds)
    {
        $parentEntityItems = array();
        foreach ($itemsData as $itemData) {
            $parentEntityItems[] = $this->_prepareParentEntityItem($itemData);
        }

        $parentEntity = new Varien_Object();
        $parentEntity->setAllItems($parentEntityItems);

        $orderItem = new Varien_Object();
        $orderItem->setId($orderItemId);

        $valueModel = new Varien_Object();
        $valueModel->setOrderItem($orderItem);

        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Item_FakeAbstract')
            ->disableOriginalConstructor()
            ->setMethods(array('_getParentEntity', '_getVariableModel'))
            ->getMock();

        $entity->expects($this->any())
            ->method('_getParentEntity')
            ->will($this->returnValue($parentEntity));

        $entity->expects($this->exactly(count($expectedOrderItemIds)))
            ->method('_getVariableModel')
            ->will($this->returnCallback(array($this, 'getVariableModelCallback')));

        $entity->__construct($valueModel);
        $actualChildren = $entity->getChildren();
        foreach ($actualChildren as $orderItemId => $actualChild) {
            $this->assertInstanceOf(
                'Saas_PrintedTemplate_Model_Variable_Abstract',
                $actualChild
            );

            $this->assertContains($orderItemId, $expectedOrderItemIds);

        }
    }

    /**
     * Prepare variable item's parent entity fake
     *
     * @param array $itemData
     * @return Varien_Object
     */
    protected function _prepareParentEntityItem($itemData)
    {

        $orderItem = new Varien_Object();
        $orderItem->setId($itemData['orderItem']['id']);
        if (isset($itemData['orderItem']['parentItem'])) {
            $parentItem = new Varien_Object();
            $parentItem->setId($itemData['orderItem']['parentItem']['id']);

            $orderItem->setParentItem($parentItem);
        }

        $item = new Varien_Object();
        $item->setOrderItem($orderItem);
        $item->setOrderItemId($itemData['orderItemId']);

        return $item;
    }

    /**
     * Get variable model callback
     *
     * @return Saas_PrintedTemplate_Model_Variable_Abstract
     */
    public function getVariableModelCallback()
    {
        $abstractVariable = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Abstract')
            ->disableOriginalConstructor()
            ->getMock();

        return $abstractVariable;
    }

    /**
     * Data provider for get children provider
     *
     * @return array
     */
    public function getChildrenProvider()
    {
        $fixturePath = __DIR__ . '/../../../_files/';
        return require_once($fixturePath . 'order_data.php');
    }

    /**
     * Format currency test
     *
     * @dataProvider formatCurrencyProvider
     */
    public function testFormatCurrency($value, $expectedResult)
    {
        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('formatPriceTxt'))
            ->getMock();

        $order->expects($this->any())
            ->method('formatPriceTxt')
            ->with($this->equalTo($value))
            ->will($this->returnCallback(array($this, 'formatPriceTxt')));

        $parentEntity = new Varien_Object();
        $parentEntity->setOrder($order);

        $valueModel = new Varien_Object();

        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Item_FakeAbstract')
            ->disableOriginalConstructor()
            ->setMethods(array('_getParentEntity', '_getVariableModel'))
            ->getMock();

        $entity->expects($this->any())
            ->method('_getParentEntity')
            ->will($this->returnValue($parentEntity));

        $entity->__construct($valueModel);
        $actualResult = $entity->formatCurrency($value);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Format currency data provider
     *
     * @return array
     */
    public function formatCurrencyProvider()
    {
        return array(
            array('1.1', $this->formatPriceTxt('1.1')),
            array(1.1, $this->formatPriceTxt(1.1)),
            array(1, $this->formatPriceTxt(1)),
            array('', $this->formatPriceTxt('')),
            array(false, $this->formatPriceTxt(false)),
            array(null, null),
        );
    }

    /**
     * Callback for formatPriceTxt method
     *
     * @param string $value
     * @return string Suffixed value
     */
    public function formatPriceTxt($value)
    {
        return $value . '_formatted';
    }

    /**
     * Format Tax Rates test
     *
     * @param array $taxPercents
     * @param string $expectedResult
     *
     * @dataProvider formatTaxRatesProvider
     */
    public function testFormatTaxRates($taxPercents, $expectedResult)
    {
        $taxes = array();
        foreach ($taxPercents as $taxPercent) {
            $tax = new Varien_Object();
            $tax->setPercent($taxPercent);

            $taxes[] = $tax;
        }

        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Item_FakeAbstract')
            ->disableOriginalConstructor()
            ->setMethods(array('_getParentEntity', '_getVariableModel', '_getLocale'))
            ->getMock();

        $entity->expects($this->any())
            ->method('_getLocale')
            ->will($this->returnValue('en_US'));

        $formattedRates = $entity->formatTaxRates($taxes);

        $this->assertEquals($expectedResult, $formattedRates);
    }

    /**
     * Dataprovider for format tax rates test
     *
     * @return array
     */
    public function formatTaxRatesProvider()
    {
        return array(
            array(array(), '0%'),
            array(array('0'), '0%'),
            array(array('0%'), '0%'),
            array(array('1%'), '1%'),
            array(array(1, 2), '1%<br />2%'),
            array(array(1.00, 2.12), '1%<br />2.12%')
        );
    }
}
