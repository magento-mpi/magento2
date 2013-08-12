<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Items_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Sales_Helper_Data
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Adminhtml_Block_Sales_Order_Create_Items_Grid
     */
    protected $_block;

    /**
     * Initialize required data
     */
    public function setUp()
    {
        $this->_helperMock = $this->getMockBuilder('Magento_Sales_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();

        $helperFactory = $this->getMockBuilder('Magento_Core_Model_Factory_Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($this->_helperMock));

        $contextMock = $this->getMockBuilder('Mage_Backend_Block_Template_Context')
            ->disableOriginalConstructor()
            ->setMethods(array('getHelperFactory'))
            ->getMock();
        $contextMock->expects($this->any())->method('getHelperFactory')->will($this->returnValue($helperFactory));
        $this->_block = $this->getMockBuilder('Magento_Adminhtml_Block_Sales_Order_Create_Items_Grid')
            ->setConstructorArgs(array($contextMock))
            ->setMethods(array('_getSession'))
            ->getMock();
        $sessionMock = $this->getMockBuilder('Magento_Adminhtml_Model_Session_Quote')
            ->disableOriginalConstructor()
            ->setMethods(array('getQuote'))
            ->getMock();
        $quoteMock = $this->getMockBuilder('Magento_Sales_Model_Quote')
            ->disableOriginalConstructor()
            ->setMethods(array('getStore'))
            ->getMock();

        $storeMock = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->setMethods(array('convertPrice'))
            ->getMock();
        $storeMock->expects($this->any())->method('convertPrice')->will($this->returnArgument(0));

        $quoteMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));

        $sessionMock->expects($this->any())->method('getQuote')->will($this->returnValue($quoteMock));
        $this->_block->expects($this->any())->method('_getSession')->will($this->returnValue($sessionMock));
    }

    /**
     * @param array $itemData
     * @param string $expectedMessage
     * @param string $productType
     * @dataProvider tierPriceDataProvider
     */
    public function testTierPriceInfo($itemData, $expectedMessage, $productType)
    {
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));

        $itemMock = $this->_prepareItem($itemData, $productType);
        $result = $this->_block->getTierHtml($itemMock);
        $this->assertEquals($expectedMessage, $result);
    }

    /**
     * Provider for test
     *
     * @return array
     */
    public function tierPriceDataProvider()
    {
        $endSign = '<br/>';
        $bundleMessage = '%1$s with %2$s discount each';
        $defaultMessage = '%s for %s';
        $bundleMessages = $bundleMessage . $endSign . $bundleMessage;
        $defaultMessages = $defaultMessage . $endSign . $defaultMessage;

        return array(
            array(
                array(array('price' => 100, 'price_qty' => 1)),
                $bundleMessage,
                Magento_Catalog_Model_Product_Type::TYPE_BUNDLE
            ),
            array(
                array(array('price' => 100, 'price_qty' => 1), array('price' => 200, 'price_qty' => 2)),
                $bundleMessages,
                Magento_Catalog_Model_Product_Type::TYPE_BUNDLE
            ),
            array(
                array(array('price' => 50, 'price_qty' => 2)),
                $defaultMessage,
                Magento_Catalog_Model_Product_Type::TYPE_SIMPLE
            ),
            array(
                array(array('price' => 50, 'price_qty' => 2), array('price' => 150, 'price_qty' => 3)),
                $defaultMessages,
                Magento_Catalog_Model_Product_Type::TYPE_SIMPLE
            ),
            array(
                0,
                '',
                Magento_Catalog_Model_Product_Type::TYPE_SIMPLE
            ),
        );
    }

    /**
     * @param array|int $tierPrices
     * @param string $productType
     * @return PHPUnit_Framework_MockObject_MockObject|Magento_Sales_Model_Quote_Item
     */
    protected function _prepareItem($tierPrices, $productType)
    {
        $product = $this->getMockBuilder('Magento_Catalog_Model_Product')
            ->disableOriginalConstructor()
            ->setMethods(array('getTierPrice'))
            ->getMock();
        $product->expects($this->once())
            ->method('getTierPrice')
            ->will($this->returnValue($tierPrices));
        $item = $this->getMockBuilder('Magento_Sales_Model_Quote_Item')
            ->disableOriginalConstructor()
            ->setMethods(array('getProduct', 'getProductType'))
            ->getMock();
        $item->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $calledTimes = $tierPrices ? 'once' : 'never';
        $item->expects($this->$calledTimes())
            ->method('getProductType')
            ->will($this->returnValue($productType));
        return $item;
    }
}
