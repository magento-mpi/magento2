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

namespace Magento\Catalog\Model;

/**
 * Tests product model:
 * - pricing behaviour is tested
 *
 * @see \Magento\Catalog\Model\ProductTest
 * @see \Magento\Catalog\Model\ProductExternalTest
 */
class ProductPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Catalog\Model\Product');
    }

    public function testGetPrice()
    {
        $this->assertEmpty($this->_model->getPrice());
        $this->_model->setPrice(10.0);
        $this->assertEquals(10.0, $this->_model->getPrice());
    }

    public function testGetPriceModel()
    {
        $default = $this->_model->getPriceModel();
        $this->assertInstanceOf('Magento\Catalog\Model\Product\Type\Price', $default);
        $this->assertSame($default, $this->_model->getPriceModel());

        $this->_model->setTypeId('configurable');
        $type = $this->_model->getPriceModel();
        $this->assertInstanceOf('Magento\Catalog\Model\Product\Type\Configurable\Price', $type);
        $this->assertSame($type, $this->_model->getPriceModel());
    }

    /**
     * See detailed tests at \Magento\Catalog\Model\Product\Type*_PriceTest
     */
    public function testGetTierPrice()
    {
        $this->assertEquals(array(), $this->_model->getTierPrice());
    }

    /**
     * See detailed tests at \Magento\Catalog\Model\Product\Type*_PriceTest
     */
    public function testGetTierPriceCount()
    {
        $this->assertEquals(0, $this->_model->getTierPriceCount());
    }

    /**
     * See detailed tests at \Magento\Catalog\Model\Product\Type*_PriceTest
     */
    public function testGetFormatedTierPrice()
    {
        $this->assertEquals(array(), $this->_model->getFormatedTierPrice());
    }

    /**
     * See detailed tests at \Magento\Catalog\Model\Product\Type*_PriceTest
     */
    public function testGetFormatedPrice()
    {
        $this->assertEquals('<span class="price">$0.00</span>', $this->_model->getFormatedPrice());
    }

    public function testSetGetFinalPrice()
    {
        $this->assertEquals(0, $this->_model->getFinalPrice());
        $this->_model->setFinalPrice(10);
        $this->assertEquals(10, $this->_model->getFinalPrice());
    }
}
