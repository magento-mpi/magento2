<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Sales\Sales;

/**
 * @magentoAppArea adminhtml
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates and inits block
     *
     * @param string|null $reportType
     * @return \Magento\Reports\Block\Adminhtml\Sales\Sales\Grid
     */
    protected function _createBlock($reportType = null)
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Reports\Block\Adminhtml\Sales\Sales\Grid'
        );

        $filterData = new \Magento\Framework\Object();
        if ($reportType) {
            $filterData->setReportType($reportType);
        }
        $block->setFilterData($filterData);

        return $block;
    }

    /**
     * @return string
     */
    public function testGetResourceCollectionNameNormal()
    {
        $block = $this->_createBlock();
        $normalCollection = $block->getResourceCollectionName();
        $this->assertTrue(class_exists($normalCollection));

        return $normalCollection;
    }

    /**
     * @depends testGetResourceCollectionNameNormal
     * @param  string $normalCollection
     */
    public function testGetResourceCollectionNameWithFilter($normalCollection)
    {
        $block = $this->_createBlock('updated_at_order');
        $filteredCollection = $block->getResourceCollectionName();
        $this->assertTrue(class_exists($filteredCollection));

        $this->assertNotEquals($normalCollection, $filteredCollection);
    }


    /**
     * Check that grid does not contains unnecessary totals row
     *
     * @param $from string
     * @param $to string
     * @param $expectedResult bool
     *
     * @dataProvider getCountTotalsDataProvider
     * @magentoDataFixture Magento/Reports/_files/orders.php
     */
    public function testGetCountTotals($from, $to, $expectedResult)
    {
        $block = $this->_createBlock();
        $filterData = new \Magento\Framework\Object();

        $filterData->setReportType('updated_at_order');
        $filterData->setPeriodType('day');
        $filterData->setData('from', $from);
        $filterData->setData('to', $to);
        $block->setFilterData($filterData);

        $block->toHtml();
        $this->assertEquals($block->getCountTotals(), $expectedResult);
    }

    /**
     * Data provider for testGetCountTotals
     *
     * @return array
     */
    public function getCountTotalsDataProvider()
    {
        return [
            [date("Y-m-d", time() + 24 * 60 * 60), date("Y-m-d", time() + 48 * 60 * 60), false],
            [date("Y-m-d", time() - 24 * 60 * 60), date("Y-m-d", time() + 24 * 60 * 60), true],
            [null, null, false],
        ];
    }
}
