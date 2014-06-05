<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Helper;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class DataTest
  */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogInventory\Helper\Data */
    protected $data;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfigMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->data = $this->objectManagerHelper->getObject(
            'Magento\CatalogInventory\Helper\Data',
            [
                'context' => $this->contextMock,
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    public function testGetConfigItemOptions()
    {
        $configOptions = [
            'min_qty',
            'backorders',
            'min_sale_qty',
            'max_sale_qty',
            'notify_stock_qty',
            'manage_stock',
            'enable_qty_increments',
            'qty_increments',
            'is_decimal_divided'
        ];
        $this->assertSame($configOptions, $this->data->getConfigItemOptions());
    }

    public function testIsShowOutOfStock()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                $this->equalTo(Data::XML_PATH_SHOW_OUT_OF_STOCK),
                $this->equalTo(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            )
            ->will($this->returnValue(true));
        $this->assertTrue($this->data->isShowOutOfStock());
    }

    public function testIsAutoReturnEnabled()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                $this->equalTo(Data::XML_PATH_ITEM_AUTO_RETURN),
                $this->equalTo(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            )
            ->will($this->returnValue(true));
        $this->assertTrue($this->data->isAutoReturnEnabled());
    }

    public function testIsDisplayProductStockStatus()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                $this->equalTo(Data::XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS),
                $this->equalTo(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            )
            ->will($this->returnValue(true));
        $this->assertTrue($this->data->isDisplayProductStockStatus());
    }
}
