<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Initialization;

/**
 * Class StockDataFilterTest
 */
class StockDataFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var StockDataFilter
     */
    protected $stockDataFilter;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $stockConfiguration;

    protected function setUp()
    {
        $this->scopeConfigMock = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');

        $this->scopeConfigMock->expects($this->any())->method('getValue')->will($this->returnValue(1));

        $this->stockConfiguration = $this->getMock(
            'Magento\CatalogInventory\Model\Configuration',
            ['getManageStock'],
            [],
            '',
            false
        );

        $this->stockDataFilter = new StockDataFilter($this->scopeConfigMock, $this->stockConfiguration);
    }

    /**
     * @param array $inputStockData
     * @param array $outputStockData
     *
     * @covers Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter::filter
     * @dataProvider filterDataProvider
     */
    public function testFilter(array $inputStockData, array $outputStockData)
    {
        if (isset($inputStockData['use_config_manage_stock']) && $inputStockData['use_config_manage_stock'] === 1) {
            $this->stockConfiguration->expects($this->once())
                ->method('getManageStock')
                ->will($this->returnValue($outputStockData['manage_stock']));
        }


        $this->assertEquals($outputStockData, $this->stockDataFilter->filter($inputStockData));
    }

    /**
     * Data provider for testFilter
     *
     * @return array
     */
    public function filterDataProvider()
    {
        return array(
            'case1' => array(
                'inputStockData' => array(),
                'outputStockData' => array('use_config_manage_stock' => 0, 'is_decimal_divided' => 0)
            ),
            'case2' => array(
                'inputStockData' => array('use_config_manage_stock' => 1),
                'outputStockData' => array(
                    'use_config_manage_stock' => 1,
                    'manage_stock' => 1,
                    'is_decimal_divided' => 0
                )
            ),
            'case3' => array(
                'inputStockData' => array(
                    'qty' => StockDataFilter::MAX_QTY_VALUE + 1
                ),
                'outputStockData' => array(
                    'qty' => StockDataFilter::MAX_QTY_VALUE,
                    'is_decimal_divided' => 0,
                    'use_config_manage_stock' => 0
                )
            ),
            'case4' => array(
                'inputStockData' => array('min_qty' => -1),
                'outputStockData' => array('min_qty' => 0, 'is_decimal_divided' => 0, 'use_config_manage_stock' => 0)
            ),
            'case5' => array(
                'inputStockData' => array('is_qty_decimal' => 0),
                'outputStockData' => array(
                    'is_qty_decimal' => 0,
                    'is_decimal_divided' => 0,
                    'use_config_manage_stock' => 0
                )
            )
        );
    }
}
