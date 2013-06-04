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

class Saas_PrintedTemplate_Model_Variable_Abstract_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test format currency
     *
     * @param int|float $value
     * @param string $expectedResult
     * @dataProvider formatCurrencyProvider
     */
    public function testFormatCurrency($value, $expectedResult)
    {
        $valueModel = new Varien_Object();
        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('formatPriceTxt'))
            ->getMock();

        $order->expects($this->once())
            ->method('formatPriceTxt')
            ->with($this->equalTo($value))
            ->will($this->returnValue($expectedResult));

        $valueModel->setOrder($order);

        $entity = new Saas_PrintedTemplate_Model_Variable_Abstract_Entity($valueModel);
        $actualResult = $entity->formatCurrency($value);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function formatCurrencyProvider()
    {
        return array(
            array(1, '$1.00'),
            array(1.00, '$1.00'),
            array(0, '$0'),
        );
    }

    /**
     * Test format empty currency
     */
    public function testFormatEmptyCurrency()
    {
        $valueModel = new Varien_Object();
        $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('formatPriceTxt'))
            ->getMock();

        $entity = new Saas_PrintedTemplate_Model_Variable_Abstract_Entity($valueModel);
        $actualResult = $entity->formatCurrency(null);

        $this->assertEquals(null, $actualResult);
    }

    /**
     * Test format base currency
     *
     * @param int|float $value
     * @param string $expectedResult
     * @dataProvider formatBaseCurrencyProvider
     */
    public function testFormatBaseCurrency($value, $expectedResult)
    {
        $valueModel = new Varien_Object();
        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('formatBasePrice'))
            ->getMock();

        $order->expects($this->once())
            ->method('formatBasePrice')
            ->with($this->equalTo($value))
            ->will($this->returnValue($expectedResult));

        $valueModel->setOrder($order);

        $entity = new Saas_PrintedTemplate_Model_Variable_Abstract_Entity($valueModel);
        $actualResult = $entity->formatBaseCurrency($value);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function formatBaseCurrencyProvider()
    {
        return array(
            array(1, '$1.00'),
            array(1.00, '$1.00'),
            array(0, '$0'),
        );
    }

    /**
     * @dataProvider getItemsProvider
     */
    public function testGetItems($valueItems, $expectedCount)
    {
        $itemsTaxes = $this->getMockBuilder('Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection')
            ->disableOriginalConstructor()
            ->setMethods(array('getItemsByColumnValue'))
            ->getMock();

        $getItemsValueMap = array();
        $items = array();
        foreach ($valueItems as $valueItem) {
            $orderItem = $this->getMockBuilder('Mage_Sales_Model_Order_Item')
                ->disableOriginalConstructor()
                ->setMethods(array('getParentItemId'))
                ->getMock();

            $orderItem->expects($this->once())
                ->method('getParentItemId')
                ->will($this->returnValue($valueItem['orderItemParentId']));

            $item = new Varien_Object();
            $item->setOrderItemId($valueItem['orderItemId']);
            $item->setOrderItem($orderItem);
            $getItemsValueMap[] = array(
                'item_id', $valueItem['orderItemId'], $valueItem['itemsByColumnValue']
            );
            $items[] = $item;
        }

        $itemsTaxes->expects($this->any())
            ->method('getItemsByColumnValue')
            ->will($this->returnValueMap($getItemsValueMap));

        $valueModel = new Varien_Object();
        $valueModel->setData(
            Saas_PrintedTemplate_Model_Variable_Abstract_Entity::TAXES_GROUPED_BY_PERCENT_CACHE_KEY,
            array('items_taxes' => $itemsTaxes)
        );

        $valueModel->setAllItems($items);
        $variableItemModel = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Abstract')
            ->disableOriginalConstructor()
            ->getMock();

        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Abstract_Entity')
            ->disableOriginalConstructor()
            ->setMethods(array('_getVariableItemModel'))
            ->getMock();

        $entity->expects($this->exactly($expectedCount))
            ->method('_getVariableItemModel')
            ->will($this->returnValue($variableItemModel));

        $entity->__construct($valueModel);

        $actualItems = $entity->getItems();
        foreach ($actualItems as $actualItem) {
            $this->assertInstanceOf('Saas_PrintedTemplate_Model_Variable_Abstract', $actualItem);
        }
    }

    public function getItemsProvider()
    {
        return array(
            array(
                array(
                    array(
                        'orderItemId' => 2,
                        'itemsByColumnValue' => array(5, 6, 7, 8),
                        'orderItemParentId' => null
                    )
                ), 1
            ),
            array(
                array(
                    array(
                        'orderItemId' => 1,
                        'itemsByColumnValue' => array(1, 2, 3, 4),
                        'orderItemParentId' => null
                    ),
                    array(
                        'orderItemId' => 2,
                        'itemsByColumnValue' => array(5, 6, 7, 8),
                        'orderItemParentId' => null
                    )
                ), 2
            ),
            array(
                array(
                    array(
                        'orderItemId' => 1,
                        'itemsByColumnValue' => array(1, 2, 3, 4),
                        'orderItemParentId' => 1
                    )
                ), 0
            ),
            array(
                array(), 0
            )
        );
    }

    /**
     * Test get taxes groupped by percent
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @dataProvider taxesGroupedByPercentProvider
     */
    public function testGetTaxesGroupedByPercent($itemsTaxSettings, $shippingTaxSettings, $expectedResult)
    {
        $taxes = array();
        foreach ($itemsTaxSettings as $settings) {
            $taxes[] = $this->_prepareTax($settings);
        }

        $itemsTaxes = $this->getMockBuilder('Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection')
            ->disableOriginalConstructor()
            ->setMethods(array('getIterator'))
            ->getMock();

        $itemsTaxes->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator($taxes)));

        $taxes = array();
        foreach ($shippingTaxSettings as $settings) {
            $taxes[] = $this->_prepareTax($settings);
        }

        $shippingTaxes = $this->getMockBuilder('Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping_Collection')
            ->disableOriginalConstructor()
            ->setMethods(array('getIterator'))
            ->getMock();

        $shippingTaxes->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator($taxes)));

        $valueModel = new Varien_Object();
        $valueModel->setData(
            Saas_PrintedTemplate_Model_Variable_Abstract_Entity::TAXES_GROUPED_BY_PERCENT_CACHE_KEY,
            array('items_taxes' => $itemsTaxes, 'shipping_taxes' => $shippingTaxes)
        );

        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Abstract_Entity')
            ->disableOriginalConstructor()
            ->setMethods(array('_getTaxVariableModel'))
            ->getMock();

        $entity->expects($this->any())
            ->method('_getTaxVariableModel')
            ->will($this->returnCallback(array($this, 'initTaxVariableModel')));

        $entity->__construct($valueModel);

        $actualTaxes = $entity->getTaxesGroupedByPercent();
        foreach ($actualTaxes as $groupKey => $actualTax) {
            $this->assertArrayHasKey($groupKey, $expectedResult);

            foreach ($expectedResult[$groupKey] as $expectedFieldName => $expectedValue) {
                $this->assertEquals(
                    $expectedResult[$groupKey][$expectedFieldName], $actualTax->getData($expectedFieldName)
                );
            }
        }
    }

    /**
     * Test get taxes groupped by percent
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @dataProvider taxesGroupedByCompoundIdProvider
     */
    public function testGetTaxesGroupedByCompoundId($itemsTaxSettings, $shippingTaxSettings, $expectedResult)
    {
        $itemsTaxes = array();
        foreach ($itemsTaxSettings as $settings) {
            $itemsTaxes[] = $this->_prepareTax($settings);
        }

        $itemsTaxesModel = $this->getMockBuilder('Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection')
            ->disableOriginalConstructor()
            ->setMethods(array('getIterator'))
            ->getMock();

        $itemsTaxesModel->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator($itemsTaxes)));

        $shippingTaxes = array();
        foreach ($shippingTaxSettings as $settings) {
            $shippingTaxes[] = $this->_prepareTax($settings);
        }

        $shippingTaxesModel = $this->getMockBuilder('Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping_Collection')
            ->disableOriginalConstructor()
            ->setMethods(array('getIterator'))
            ->getMock();

        $shippingTaxesModel->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator($shippingTaxes)));

        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->getMock();

        $valueModel = new Varien_Object();
        $valueModel->setData(
            Saas_PrintedTemplate_Model_Variable_Abstract_Entity::TAXES_GROUPED_BY_PERCENT_CACHE_KEY,
            array('items_taxes' => $itemsTaxesModel, 'shipping_taxes' => $shippingTaxesModel)
        );
        $valueModel->setOrder($order);

        $entity = $this->getMockBuilder('Saas_PrintedTemplate_Model_Variable_Abstract_Entity')
            ->disableOriginalConstructor()
            ->setMethods(array('_getTaxVariableModel', '_getTaxCompoundIdModel'))
            ->getMock();

        $entity->expects($this->any())
            ->method('_getTaxVariableModel')
            ->will($this->returnCallback(array($this, 'initTaxVariableModel')));

        $entity->expects($this->any())
            ->method('_getTaxCompoundIdModel')
            ->will($this->returnValue(new Saas_PrintedTemplate_Model_Tax_CompoundId()));

        $entity->__construct($valueModel);

        $actualTaxes = $entity->getTaxesGroupedByCompoundId();
        foreach ($actualTaxes as $groupKey => $actualTax) {
            $this->assertArrayHasKey($groupKey, $expectedResult);

            foreach ($expectedResult[$groupKey] as $expectedFieldName => $expectedValue) {
                $this->assertEquals(
                    $expectedResult[$groupKey][$expectedFieldName], $actualTax->getData($expectedFieldName)
                );
            }
        }
    }

    public function taxesGroupedByCompoundIdProvider()
    {
        return array(
            array(
                array(
                    array(
                        'percent' => 0.1, 'total_amount' => 100, 'base_total_amount' => 80.99,
                        'tax_amount' => 10, 'base_tax_amount' => 8.09,
                        'row_total' => 100.00, 'discount_amount' => 10, 'priority' => 1
                    )
                ),
                array(),
                array(
                    '0.1' => array(
                        'total_amount' => 100, 'tax_amount' => 10,
                        'tax_amount_without_discount' => 10.00,
                        'total_amount_without_discount' => 90
                    )
                )
            ),


            array(
                array(
                    array(
                        'percent' => 0.1, 'total_amount' => 100, 'base_total_amount' => 80.99,
                        'is_tax_after_discount' => true, 'tax_amount' => 10, 'base_tax_amount' => 8.09,
                        'row_total' => 100.00, 'discount_amount' => 10, 'priority' => 1
                    )
                ),
                array(),
                array(
                    '0.1' => array(
                        'total_amount' => 90, 'tax_amount' => 10,
                        'tax_amount_without_discount' => 10.00, 'total_amount_without_discount' => 80
                    )
                )
            ),

            array(
                array(
                    array(
                        'percent' => 0.1, 'total_amount' => 90, 'base_total_amount' => 80.99,
                        'tax_amount' => 10, 'base_tax_amount' => 8.09, 'is_tax_after_discount' => true,
                        'row_total' => 90.00, 'discount_amount' => 10, 'priority' => 1
                    )
                ),
                array(),
                array(
                    '0.1' => array(
                        'total_amount' => 80, 'tax_amount' => 10,
                        'tax_amount_without_discount' => 10.00, 'total_amount_without_discount' => 70
                    )
                )
            ),

            array(
                array(
                    array(
                        'percent' => 0.1, 'total_amount' => 100, 'base_total_amount' => 80.99,
                        'is_tax_after_discount' => true,
                        'tax_amount' => 10, 'base_tax_amount' => 8.09,
                        'row_total' => 100.00, 'discount_amount' => 10, 'priority' => 1
                    ), array(
                        'percent' => 0.2,
                        'total_amount' => 22.33, 'base_total_amount' => 20.99,
                        'tax_amount' => 4.66, 'base_tax_amount' => 4.19, 'is_tax_after_discount' => true,
                        'row_total' => 100.00, 'discount_amount' => 10, 'priority' => 1
                    )
                ),
                array(
                    array(
                        'percent' => 0.1, 'total_amount' => 100, 'base_total_amount' => 90.00,
                        'tax_amount' => 10, 'base_tax_amount' => 8.09,
                        'row_total' => 100.00, 'discount_amount' => 10,
                        'is_tax_after_discount' => true, 'priority' => 1
                    ), array(
                        'percent' => 0.2, 'total_amount' => 22.33, 'base_total_amount' => 20.99,
                        'tax_amount' => 4.66, 'base_tax_amount' => 4.19, 'is_tax_after_discount' => true,
                        'row_total' => 100.00, 'discount_amount' => 10, 'priority' => 1
                    )
                ),
                array(
                    '0.1+0.2,0.1+0.2' => array(
                        'total_amount' => 90, 'tax_amount' => 10,
                        'tax_amount_without_discount' => 10.00, 'total_amount_without_discount' => 80,
                    ),
                )
            )
        );
    }

    protected function _prepareTax($settings)
    {
        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->getMock();

        $taxItem = new Varien_Object;
        foreach ($settings as $field => $value) {
            $taxItem->setData($field, $value);
        }

        if (!$taxItem->getOrderId()) {
            $taxItem->setOrderId("123");
        }
        $taxItem->setOrder($order);

        return $taxItem;
    }

    public function taxesGroupedByPercentProvider()
    {
        return array(

            array(
                array(
                    array(
                        'percent' => '0.1',
                        'total_amount' => 100, 'base_total_amount' => 80.99,
                        'tax_amount' => 10, 'base_tax_amount' => 8.09
                    ), array(
                        'percent' => '0.2',
                        'total_amount' => 22.33, 'base_total_amount' => 20.99,
                        'tax_amount' => 4.66, 'base_tax_amount' => 4.19
                    )
                ),
                array(
                    array(
                        'percent' => '0.1',
                        'total_amount' => 100, 'base_total_amount' => 90.00,
                        'tax_amount' => 10, 'base_tax_amount' => 8.09
                    ), array(
                        'percent' => '0.2',
                        'total_amount' => 22.33, 'base_total_amount' => 20.99,
                        'tax_amount' => 4.66, 'base_tax_amount' => 4.19
                    )
                ),
                array(
                    '0.1' => array('total_amount' => 200, 'tax_amount' => 20),
                    '0.2' => array('total_amount' =>  44.66, 'tax_amount' => 9.32)
                )
            ),

            array(
                array(
                    array(
                        'percent' => '0.1',
                        'total_amount' => 100, 'base_total_amount' => 80.99,
                        'tax_amount' => 10, 'base_tax_amount' => 8.09
                    ), array(
                        'percent' => '0.1',
                        'total_amount' => 22.33, 'base_total_amount' => 20.99,
                        'tax_amount' => 4.66, 'base_tax_amount' => 4.19
                    ), array(
                        'percent' => '0.1',
                        'total_amount' => 100, 'base_total_amount' => 90.00,
                        'tax_amount' => 10, 'base_tax_amount' => 8.09
                    )
                ),
                array(),
                array(
                    '0.1' => array('total_amount' => 222.33, 'tax_amount' => 24.66),
                )
            ),

            array(
                array(), array(), array()
            )

        );
    }

    public function initTaxVariableModel($args)
    {
        return new Saas_PrintedTemplate_Model_Variable_FakeTax($args['value']);
    }

    public function formatCurrenyCallback($value)
    {
        return $value;
    }

}
