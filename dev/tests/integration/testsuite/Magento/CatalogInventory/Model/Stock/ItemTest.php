<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogInventory_Model_Stock_ItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_CatalogInventory_Model_Stock_Item
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_CatalogInventory_Model_Stock_Item');
    }

    /**
     * Simple product with stock item
     */
    public static function simpleProductFixture()
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->setTypeId('simple')
            ->setId(1)
            ->setAttributeSetId(4)
            ->setName('Simple Product')
            ->setSku('simple')
            ->setPrice(10)
            ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->save();
    }

    /**
     * @magentoDataFixture simpleProductFixture
     */
    public function testSaveWithNullQty()
    {
        $this->_model
            ->setProductId(1)
            ->setTypeId(Magento_Catalog_Model_Product_Type::DEFAULT_TYPE)
            ->setStockId(Magento_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID)
            ->setQty(null);
        $this->_model->save();

        $this->_model->setQty(2);
        $this->_model->save();
        $this->assertEquals('2.0000', $this->_model->load(1)->getQty());

        $this->_model->setQty(0);
        $this->_model->save();
        $this->assertEquals('0.0000', $this->_model->load(1)->getQty());

        $this->_model->setQty(null);
        $this->_model->save();
        $this->assertEquals(null, $this->_model->load(1)->getQty());
    }

    /**
     * @magentoDataFixture simpleProductFixture
     */
    public function testStockStatusChangedAuto()
    {
        $this->_model
            ->setProductId(1)
            ->setTypeId(Magento_Catalog_Model_Product_Type::DEFAULT_TYPE)
            ->setStockId(Magento_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID)
            ->setQty(1);
        $this->_model->save();
        $this->assertEquals(0, $this->_model->getStockStatusChangedAuto());

        $this->_model->setStockStatusChangedAutomaticallyFlag(1);
        $this->_model->save();
        $this->assertEquals(1, $this->_model->getStockStatusChangedAuto());
    }

    /**
     * @magentoConfigFixture current_store cataloginventory/item_options/enable_qty_increments 1
     */
    public function testSetGetEnableQtyIncrements()
    {
        $this->assertFalse($this->_model->getEnableQtyIncrements());

        $this->_model->setUseConfigEnableQtyInc(true);
        $this->assertTrue($this->_model->getEnableQtyIncrements());
    }

    public function testSetGetProduct()
    {
        $this->assertNull($this->_model->getProduct());
        $productOne = new Magento_Object;
        $this->_model->setData('product', $productOne);
        $this->assertSame($productOne, $this->_model->getProduct());

        $productTwo = new Magento_Object;
        $this->_model->setProduct($productTwo);
        $this->assertSame($productTwo, $this->_model->getProduct());
    }
}
