<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Filter\DataProvider;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\DataProvider\Price.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class PriceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
     */
    protected $_model;

    protected function setUp()
    {
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Category'
        );
        $category->load(4);
        $layer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('\Magento\Catalog\Model\Layer\Category');
        $layer->setCurrentCategory($category);
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Filter\DataProvider\Price', ['layer' => $layer]);
    }

    /**
     * @magentoConfigFixture current_store catalog/layered_navigation/price_range_calculation auto
     */
    public function testGetPriceRangeAuto()
    {
        $this->assertEquals(10, $this->_model->getPriceRange());
    }

    /**
     * @magentoConfigFixture current_store catalog/layered_navigation/price_range_calculation manual
     * @magentoConfigFixture current_store catalog/layered_navigation/price_range_step 1.5
     */
    public function testGetPriceRangeManual()
    {
        // what you set is what you get
        $this->assertEquals(1.5, $this->_model->getPriceRange());
    }

    public function testGetMaxPriceInt()
    {
        $this->assertEquals(45.00, $this->_model->getMaxPrice());
    }

    /**
     * @return array
     */
    public function getRangeItemCountsDataProvider()
    {
        return [
            [1, [11 => 2, 46 => 1]],
            [10, [2 => 2, 5 => 1]],
            [20, [1 => 2, 3 => 1]],
            [50, [1 => 3]]
        ];
    }

    /**
     * @dataProvider getRangeItemCountsDataProvider
     */
    public function testGetRangeItemCounts($inputRange, $expectedItemCounts)
    {
        $actualItemCounts = $this->_model->getRangeItemCounts($inputRange);
        $this->assertEquals($expectedItemCounts, $actualItemCounts);
    }
}
