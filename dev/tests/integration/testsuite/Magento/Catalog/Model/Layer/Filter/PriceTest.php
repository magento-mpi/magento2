<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Filter;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Price.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class PriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Price
     */
    protected $_model;

    protected function setUp()
    {
        $category = \Mage::getModel('Magento\Catalog\Model\Category');
        $category->load(4);
        $this->_model = \Mage::getModel('Magento\Catalog\Model\Layer\Filter\Price');
        $this->_model->setData(array(
            'layer' => \Mage::getModel('Magento\Catalog\Model\Layer', array(
                'data' => array('current_category' => $category)
            )),
        ));
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
        $this->assertEquals(45.00, $this->_model->getMaxPriceInt());
    }

    public function getRangeItemCountsDataProvider()
    {
        return array(
            array(1,  array(11 => 1, 46 => 1)),
            array(10, array(2  => 1, 5  => 1)),
            array(20, array(1  => 1, 3  => 1)),
            array(50, array(1  => 2)),
        );
    }

    /**
     * @dataProvider getRangeItemCountsDataProvider
     */
    public function testGetRangeItemCounts($inputRange, $expectedItemCounts)
    {
        $this->assertEquals($expectedItemCounts, $this->_model->getRangeItemCounts($inputRange));
    }

    public function testApplyNothing()
    {
        $this->assertEmpty($this->_model->getData('price_range'));

        $this->_model->apply(
            new \Magento\TestFramework\Request(),
            \Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text')
        );

        $this->assertEmpty($this->_model->getData('price_range'));
    }

    public function testApplyInvalid()
    {
        $this->assertEmpty($this->_model->getData('price_range'));

        $request = new \Magento\TestFramework\Request();
        $request->setParam('price', 'non-numeric');
        $this->_model->apply($request, \Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text'));

        $this->assertEmpty($this->_model->getData('price_range'));
    }

    /**
     * @magentoConfigFixture current_store catalog/layered_navigation/price_range_calculation manual
     */
    public function testApplyManual()
    {
        $request = new \Magento\TestFramework\Request();
        $request->setParam('price', '10-20');
        $this->_model->apply($request, \Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text'));

        $this->assertEquals(array(10, 20), $this->_model->getData('interval'));
    }

    public function testGetSetCustomerGroupId()
    {
        $this->assertEquals(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID, $this->_model->getCustomerGroupId());

        $customerGroupId = 123;
        $this->_model->setCustomerGroupId($customerGroupId);

        $this->assertEquals($customerGroupId, $this->_model->getCustomerGroupId());
    }

    public function testGetSetCurrencyRate()
    {
        $this->assertEquals(1, $this->_model->getCurrencyRate());

        $currencyRate = 42;
        $this->_model->setCurrencyRate($currencyRate);

        $this->assertEquals($currencyRate, $this->_model->getCurrencyRate());
    }
}
