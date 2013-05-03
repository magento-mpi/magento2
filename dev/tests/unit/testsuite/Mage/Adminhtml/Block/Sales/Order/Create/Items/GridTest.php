<?php
/**
 * Testcase for Mage_Adminhtml_Block_Sales_Order_Create_Items_Stub_Grid class.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Items_Stub_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider getTierHtmlDataProvider
     *
     * @param array $tierPrices
     * @param string $productType
     * @param string $method
     * @param array $tierPriceInfo
     * @param string $expectedResult
     */
    public function testGetTierHtml($tierPrices, $productType, $method, $tierPriceInfo, $expectedResult)
    {
        $testObject = $this->getMockBuilder('Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid')
            ->disableOriginalConstructor()
            ->setMethods(array('_getBundleTierPriceInfo', '_getTierPriceInfo'))
            ->getMock();
        $testObject->expects($this->once())
            ->method($method)
            ->with($this->equalTo($tierPrices))
            ->will($this->returnValue($tierPriceInfo));
        $item = $this->_prepareItem($tierPrices, $productType);
        $this->assertEquals($expectedResult, $testObject->getTierHtml($item));
    }

    /**
     * Prepare mock of Mage_Sales_Model_Quote_Item
     *
     * @param array $tierPrices
     * @param string $productType
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareItem($tierPrices, $productType)
    {
        $product = $this->getMockBuilder('Mage_Catalog_Model_Product')
            ->disableOriginalConstructor()
            ->setMethods(array('getTierPrice'))
            ->getMock();
        $product->expects($this->once())
            ->method('getTierPrice')
            ->will($this->returnValue($tierPrices));
        $item = $this->getMockBuilder('Mage_Sales_Model_Quote_Item')
            ->disableOriginalConstructor()
            ->setMethods(array('getProduct', 'getProductType', '_getBundleTierPriceInfo', '_getTierPriceInfo'))
            ->getMock();
        $item->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));
        $item->expects($this->once())
            ->method('getProductType')
            ->will($this->returnValue($productType));
        return $item;
    }

    /**
     * Data provider for testGetTierHtml
     *
     * @return array
     */
    public static function getTierHtmlDataProvider()
    {
        return array(
            array(
                array('price_qty' => 2.0000, 'price' => 50.0000),
                'bundle',
                '_getBundleTierPriceInfo',
                array('1', '2', '3'),
                '1<br/>2<br/>3'
            ),
            array(
                array('price_qty' => 5.0000, 'price' => 10.0000),
                'configurable',
                '_getTierPriceInfo',
                array('1'),
                '1'
            )
        );
    }

    /**
     * Test for _getBundleTierPriceInfo method
     *
     * @test
     * @dataProvider getBundleTierPriceInfoDataProvider
     *
     * @param array $prices
     * @param string $expectedResult
     */
    public function testGetBundleTierPriceInfo($prices, $expectedResult)
    {
        $returnCallback = function() {
            $arguments = func_get_args();
            return @vsprintf(array_shift($arguments), $arguments);
        };
        $helper = $this->getMockBuilder('Mage_Sales_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnCallback($returnCallback));
        $helperFactory = $this->getMockBuilder('Mage_Core_Model_Factory_Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->exactly(count($prices)))
            ->method('get')
            ->with('Mage_Sales_Helper_Data')
            ->will($this->returnValue($helper));
        $testObjectStub = $this->getMockBuilder('Mage_Adminhtml_Block_Sales_Order_Create_Items_Stub_Grid')
            ->disableOriginalConstructor()
            ->setMethods(array('convertPrice'))
            ->getMock();
        $testObjectStub->_helperFactory = $helperFactory;
        $this->assertEquals($expectedResult, $testObjectStub->getBundleTierPriceInfo($prices));
    }

    /**
     * Data provider for testGetBundleTierPriceInfo
     *
     * @return array
     */
    public static function getBundleTierPriceInfoDataProvider()
    {
        return array(
            array(
                array(
                    array('price_qty' => 2.00, 'price' => 50.0000)
                ),
                array('2 with 50% discount each')
            ),
            array(
                array(
                    array('price_qty' => 2.00, 'price' => 50.0000),
                    array('price_qty' => 5.00, 'price' => 55.5000)
                ),
                array('2 with 50% discount each', '5 with 55.5% discount each')
            )
        );
    }

    /**
     * Test for _getTierPriceInfo method
     *
     * @test
     * @dataProvider getTierPriceInfoDataProvider
     *
     * @param array $prices
     * @param string $expectedResult
     */
    public function testGetTierPriceInfo($prices, $expectedResult)
    {
        $returnCallback = function() {
            $arguments = func_get_args();
            return @vsprintf(array_shift($arguments), $arguments);
        };
        $helper = $this->getMockBuilder('Mage_Sales_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnCallback($returnCallback));
        $helperFactory = $this->getMockBuilder('Mage_Core_Model_Factory_Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->exactly(count($prices)))
            ->method('get')
            ->with('Mage_Sales_Helper_Data')
            ->will($this->returnValue($helper));
        $testObjectStub = $this->getMockBuilder('Mage_Adminhtml_Block_Sales_Order_Create_Items_Stub_Grid')
            ->disableOriginalConstructor()
            ->setMethods(array('convertPrice'))
            ->getMock();
        $testObjectStub->_helperFactory = $helperFactory;
        $testObjectStub->expects($this->exactly(count($prices)))
            ->method('convertPrice')
            ->will($this->returnArgument(0));
        $this->assertEquals($expectedResult, $testObjectStub->getTierPriceInfo($prices));
    }

    /**
     * Data provider for testGetTierPriceInfo
     *
     * @return array
     */
    public static function getTierPriceInfoDataProvider()
    {
        return array(
            array(
                array(
                    array('price_qty' => 7.00, 'price' => 10.0000)
                ),
                array('7 for 10')
            ),
            array(
                array(
                    array('price_qty' => 5.00, 'price' => 10.0000),
                    array('price_qty' => 15.00, 'price' => 7.2500)
                ),
                array('5 for 10', '15 for 7.25')
            )
        );
    }
}
