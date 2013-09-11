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
 * Test class for \Magento\Catalog\Block\Product\ProductList\Related.
 *
 * @magentoDataFixture Magento/Catalog/_files/products_related.php
 */
class Magento_Catalog_Block_Product_ProductList_RelatedTest extends PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        Mage::app()->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(2);
        Mage::register('product', $product);
        /** @var $block \Magento\Catalog\Block\Product\ProductList\Related */
        $block = Mage::app()->getLayout()->createBlock('Magento\Catalog\Block\Product\ProductList\Related');
        $block->setLayout(Mage::getModel('Magento\Core\Model\Layout'));
        $block->setTemplate('product/list/related.phtml');

        $html = $block->toHtml();
        $this->assertNotEmpty($html);
        $this->assertContains('Simple Related Product', $html); /* name */
        $this->assertContains('product/1/', $html);  /* part of url */
        $this->assertInstanceOf('\Magento\Catalog\Model\Resource\Product\Link\Product\Collection', $block->getItems());
    }
}
