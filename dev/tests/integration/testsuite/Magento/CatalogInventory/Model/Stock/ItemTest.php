<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Stock;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Stock\Item
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogInventory\Model\Stock\Item'
        );
    }

    /**
     * Simple product with stock item
     */
    public static function simpleProductFixture()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->setTypeId('simple')
            ->setId(1)
            ->setAttributeSetId(4)
            ->setName('Simple Product')
            ->setSku('simple')
            ->setPrice(10)
            ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->save();
    }

    /**
     * @magentoDataFixture simpleProductFixture
     */
    public function testSaveWithNullQty()
    {
        $this->_model->setProductId(1)
            ->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE)
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
        $this->_model->setProductId(1)
            ->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE)
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
}
