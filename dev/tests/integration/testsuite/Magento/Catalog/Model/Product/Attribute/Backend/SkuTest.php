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


namespace Magento\Catalog\Model\Product\Attribute\Backend;

/**
 * Test class for \Magento\Catalog\Model\Product\Attribute\Backend\Sku.
 * @magentoAppArea adminhtml
 */
class SkuTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testGenerateUniqueSkuExistingProduct()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->load(1);
        $product->setId(null);
        $this->assertEquals('simple', $product->getSku());
        $product->getResource()->getAttribute('sku')->getBackend()->beforeSave($product);
        $this->assertEquals('simple-1', $product->getSku());
    }

    /**
     * @param $product \Magento\Catalog\Model\Product
     * @dataProvider uniqueSkuDataProvider
     */
    public function testGenerateUniqueSkuNotExistingProduct($product)
    {
        $this->assertEquals('simple', $product->getSku());
        $product->getResource()->getAttribute('sku')->getBackend()->beforeSave($product);
        $this->assertEquals('simple', $product->getSku());
    }

    /**
     * @param $product \Magento\Catalog\Model\Product
     * @dataProvider uniqueLongSkuDataProvider
     * @magentoAppArea adminhtml
     * @magentoDbIsolation enabled
     */
    public function testGenerateUniqueLongSku($product)
    {
        $product->duplicate();
        $this->assertEquals('0123456789012345678901234567890123456789012345678901234567890123', $product->getSku());
        $product->getResource()->getAttribute('sku')->getBackend()->beforeSave($product);
        $this->assertEquals('01234567890123456789012345678901234567890123456789012345678901-1', $product->getSku());
    }

    /**
     * Returns simple product
     *
     * @return array
     */
    public function uniqueSkuDataProvider()
    {
        $product = $this->_getProduct();
        return array(array($product));
    }

    /**
     * Returns simple product
     *
     * @return array
     */
    public function uniqueLongSkuDataProvider()
    {
        $product = $this->_getProduct();
        $product->setSku('0123456789012345678901234567890123456789012345678901234567890123'); //strlen === 64
        return array(array($product));
    }

    /**
     * Get product form data provider
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function _getProduct()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
            ->setId(1)
            ->setAttributeSetId(4)
            ->setWebsiteIds(array(1))
            ->setName('Simple Product')
            ->setSku('simple')
            ->setPrice(10)
            ->setDescription('Description with <b>html tag</b>')
            ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
            ->setCategoryIds(array(2))
            ->setStockData(
                array(
                    'use_config_manage_stock' => 1,
                    'qty' => 100,
                    'is_qty_decimal' => 0,
                    'is_in_stock' => 1,
                )
            );
        return $product;
    }
}
