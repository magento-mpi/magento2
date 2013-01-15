<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_Variable_Item_ShipmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test get children method
     *
     * @param int orderItemId
     * @param array $itemsData
     * @param array $expectedItems
     * @dataProvider getChildrenProvider
     * @test
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

        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Item_Shipment')
            ->disableOriginalConstructor()
            ->setMethods(array('_getParentEntity', '_getVariableModel', '_setListsFromConfig'))
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
     * @todo: process case of children items
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
     * Data provider for get children provider
     *
     * @return array
     */
    public function getChildrenProvider()
    {
        return array(
            '1/1' => array(
                1, array(
                    array(
                        'orderItem' => array(
                            'id' => 1,
                            'parentItem' => array(
                                'id' => 1
                            ),
                        ),
                        'orderItemId' => 1
                    )
                ), array(1)
            ),

            '1/1 - by order item\'s parent id' => array(
                1, array(
                    array(
                        'orderItem' => array(
                            'id' => 2,
                            'parentItem' => array(
                                'id' => 1
                            ),
                        ),
                        'orderItemId' => 1
                    )
                ), array(1)
            ),

            '0/1' => array(
                2, array(
                    array(
                        'orderItem' => array(
                            'id' => 1,
                            'parentItem' => array(
                                'id' => 1
                            ),
                        ),
                        'orderItemId' => 1
                    )
                ), array()
            ),

            '2/2' => array(
                1, array(
                    array(
                        'orderItem' => array(
                            'id' => 1
                        ),
                        'orderItemId' => 1
                    ),

                    array(
                        'orderItem' => array(
                            'id' => 1,
                            'parentItem' => array(
                                'id' => 1
                            ),
                        ),
                        'orderItemId' => 2
                    )
                ), array(1, 2)
            )
        );
    }

    /**
     * Get variable model callback
     *
     * @param array $arguments
     * @return Saas_PrintedTemplate_Model_Variable_Abstract
     */
    public function getVariableModelCallback($arguments)
    {
        $abstractVariable = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Abstract')
            ->disableOriginalConstructor()
            ->getMock();

        return $abstractVariable;
    }

}
