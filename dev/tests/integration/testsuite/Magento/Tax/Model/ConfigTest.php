<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tax_Model_Config
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Tax_Model_Config');
    }

    public function testSetPriceIncludesTax()
    {
        $this->assertFalse($this->_model->priceIncludesTax());
        $this->assertSame($this->_model, $this->_model->setPriceIncludesTax(1));
        $this->assertTrue($this->_model->priceIncludesTax());
        $this->_model->setPriceIncludesTax(null);
        $this->assertFalse($this->_model->priceIncludesTax());
    }

    /**
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 1
     */
    public function testPriceIncludesTaxNonDefault()
    {
        $this->assertTrue($this->_model->priceIncludesTax());
    }
}
