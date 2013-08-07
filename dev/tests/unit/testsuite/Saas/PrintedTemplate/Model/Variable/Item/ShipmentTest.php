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

class Saas_PrintedTemplate_Model_Variable_Item_ShipmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test get children method
     *
     * @param int orderItemId
     * @param array $itemsData
     * @param array $expectedOrderItemIds
     * @dataProvider getChildrenDataProvider
     */
    public function testGetChildren($orderItemId, $itemsData, $expectedOrderItemIds)
    {
        $parentEntityItems = array();
        foreach ($itemsData as $itemData) {
            $parentEntityItems[] = $this->_prepareParentEntityItem($itemData);
        }

        $parentEntity = new Magento_Object();
        $parentEntity->setAllItems($parentEntityItems);

        $orderItem = new Magento_Object();
        $orderItem->setId($orderItemId);

        $valueModel = new Magento_Object();
        $valueModel->setOrderItem($orderItem);

        $shipmentEntity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Item_Shipment')
            ->disableOriginalConstructor()
            ->setMethods(array('_getParentEntity', '_getVariableModel', '_setListsFromConfig'))
            ->getMock();

        $shipmentEntity->expects($this->any())
            ->method('_getParentEntity')
            ->will($this->returnValue($parentEntity));

        $shipmentEntity->expects($this->exactly(count($expectedOrderItemIds)))
            ->method('_getVariableModel')
            ->will($this->returnCallback(array($this, 'getVariableModelCallback')));

        $shipmentEntity->__construct($valueModel);
        $actualChildren = $shipmentEntity->getChildren();
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
     * @todo: process case of children items
     *
     * @param array $itemData
     * @return Magento_Object
     */
    protected function _prepareParentEntityItem($itemData)
    {
        $orderItem = new Magento_Object();
        $orderItem->setId($itemData['orderItem']['id']);
        if (isset($itemData['orderItem']['parentItem'])) {
            $parentItem = new Magento_Object();
            $parentItem->setId($itemData['orderItem']['parentItem']['id']);

            $orderItem->setParentItem($parentItem);
        }

        $item = new Magento_Object();
        $item->setOrderItem($orderItem);
        $item->setOrderItemId($itemData['orderItemId']);

        return $item;
    }

    /**
     * Data provider for get children provider
     *
     * @return array
     */
    public function getChildrenDataProvider()
    {
        $fixturePath = __DIR__ . '/../../../_files/';
        return include ($fixturePath . 'order_data.php');
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

}
