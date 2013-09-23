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

/**
 * Test class for Magento_Catalog_Model_Layer_Filter_Price.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class Magento_Catalog_Model_Layer_Filter_PriceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Layer_Filter_Price
     */
    protected $_model;

    protected function setUp()
    {
        $category = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Category');
        $category->load(4);
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer_Filter_Price');
        $this->_model->setData(array(
            'layer' => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer', array(
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
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $request Magento_TestFramework_Request */
        $request = $objectManager->get('Magento_TestFramework_Request');
        $this->_model->apply(
            $request,
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
                ->createBlock('Magento_Core_Block_Text')
        );

        $this->assertEmpty($this->_model->getData('price_range'));
    }

    public function testApplyInvalid()
    {
        $this->assertEmpty($this->_model->getData('price_range'));
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $request Magento_TestFramework_Request */
        $request = $objectManager->get('Magento_TestFramework_Request');
        $request->setParam('price', 'non-numeric');
        $this->_model->apply(
            $request,
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
                ->createBlock('Magento_Core_Block_Text')
        );

        $this->assertEmpty($this->_model->getData('price_range'));
    }

    /**
     * @magentoConfigFixture current_store catalog/layered_navigation/price_range_calculation manual
     */
    public function testApplyManual()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $request Magento_TestFramework_Request */
        $request = $objectManager->get('Magento_TestFramework_Request');
        $request->setParam('price', '10-20');
        $this->_model->apply(
            $request,
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
                ->createBlock('Magento_Core_Block_Text')
        );

        $this->assertEquals(array(10, 20), $this->_model->getData('interval'));
    }

    public function testGetSetCustomerGroupId()
    {
        $this->assertEquals(Magento_Customer_Model_Group::NOT_LOGGED_IN_ID, $this->_model->getCustomerGroupId());

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
