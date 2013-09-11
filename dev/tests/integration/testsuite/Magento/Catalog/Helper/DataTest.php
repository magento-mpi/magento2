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

class Magento_Catalog_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Magento\Catalog\Helper\Data');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testGetBreadcrumbPath()
    {
        $category = Mage::getModel('Magento\Catalog\Model\Category');
        $category->load(5);
        Mage::register('current_category', $category);

        try {
            $path = $this->_helper->getBreadcrumbPath();
            $this->assertInternalType('array', $path);
            $this->assertEquals(array('category3', 'category4', 'category5'), array_keys($path));
            $this->assertArrayHasKey('label', $path['category3']);
            $this->assertArrayHasKey('link', $path['category3']);
            Mage::unregister('current_category');
        } catch (Exception $e) {
            Mage::unregister('current_category');
            throw $e;
        }
    }

    public function testGetCategory()
    {
        $category = Mage::getModel('Magento\Catalog\Model\Category');
        Mage::register('current_category', $category);
        try {
            $this->assertSame($category, $this->_helper->getCategory());
            Mage::unregister('current_category');
        } catch (Exception $e) {
            Mage::unregister('current_category');
            throw $e;
        }
    }

    public function testGetProduct()
    {
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        Mage::register('current_product', $product);
        try {
            $this->assertSame($product, $this->_helper->getProduct());
            Mage::unregister('current_product');
        } catch (Exception $e) {
            Mage::unregister('current_product');
            throw $e;
        }
    }

    public function testSplitSku()
    {
        $sku = 'one-two-three';
        $this->assertEquals(array('on', 'e-', 'tw', 'o-', 'th', 're', 'e'), $this->_helper->splitSku($sku, 2));
    }

    public function testGetAttributeHiddenFields()
    {
        $this->assertEquals(array(), $this->_helper->getAttributeHiddenFields());
        Mage::register('attribute_type_hidden_fields', 'test');
        try {
            $this->assertEquals('test', $this->_helper->getAttributeHiddenFields());
            Mage::unregister('attribute_type_hidden_fields');
        } catch (Exception $e) {
            Mage::unregister('attribute_type_hidden_fields');
            throw $e;
        }
    }

    public function testGetAttributeDisabledTypes()
    {
        $this->assertEquals(array(), $this->_helper->getAttributeDisabledTypes());
        Mage::register('attribute_type_disabled_types', 'test');
        try {
            $this->assertEquals('test', $this->_helper->getAttributeDisabledTypes());
            Mage::unregister('attribute_type_disabled_types');
        } catch (Exception $e) {
            Mage::unregister('attribute_type_disabled_types');
            throw $e;
        }
    }

    public function testGetPriceScopeDefault()
    {
        // $this->assertEquals(\Magento\Core\Model\Store::PRICE_SCOPE_GLOBAL, $this->_helper->getPriceScope());
        $this->assertNull($this->_helper->getPriceScope());
    }

    /**
     * @magentoConfigFixture current_store catalog/price/scope 1
     */
    public function testGetPriceScope()
    {
        $this->assertEquals(\Magento\Core\Model\Store::PRICE_SCOPE_WEBSITE, $this->_helper->getPriceScope());
    }

    public function testIsPriceGlobalDefault()
    {
        $this->assertTrue($this->_helper->isPriceGlobal());
    }

    /**
     * @magentoConfigFixture current_store catalog/price/scope 1
     */
    public function testIsPriceGlobal()
    {
        $this->assertFalse($this->_helper->isPriceGlobal());
    }

    public function testShouldSaveUrlRewritesHistoryDefault()
    {
        $this->assertTrue($this->_helper->shouldSaveUrlRewritesHistory());
    }

    /**
     * @magentoConfigFixture current_store catalog/seo/save_rewrites_history 0
     */
    public function testShouldSaveUrlRewritesHistory()
    {
        $this->assertFalse($this->_helper->shouldSaveUrlRewritesHistory());
    }

    public function testIsUsingStaticUrlsAllowedDefault()
    {
        $this->assertFalse($this->_helper->isUsingStaticUrlsAllowed());
    }

    /**
     * isUsingStaticUrlsAllowed()
     * setStoreId()
     * @magentoConfigFixture current_store cms/wysiwyg/use_static_urls_in_catalog 1
     */
    public function testIsUsingStaticUrlsAllowed()
    {
        $this->assertTrue($this->_helper->isUsingStaticUrlsAllowed());
        $this->_helper->setStoreId(Mage::app()->getStore()->getId());
        $this->assertTrue($this->_helper->isUsingStaticUrlsAllowed());
    }

    public function testIsUrlDirectivesParsingAllowedDefault()
    {
        $this->assertTrue($this->_helper->isUrlDirectivesParsingAllowed());
    }

    /**
     * isUrlDirectivesParsingAllowed()
     * setStoreId()
     * @magentoConfigFixture current_store catalog/frontend/parse_url_directives 0
     */
    public function testIsUrlDirectivesParsingAllowed()
    {
        $this->assertFalse($this->_helper->isUrlDirectivesParsingAllowed());
        $this->_helper->setStoreId(Mage::app()->getStore()->getId());
        $this->assertFalse($this->_helper->isUrlDirectivesParsingAllowed());
    }

    public function testGetPageTemplateProcessor()
    {
        $this->assertInstanceOf('\Magento\Filter\Template', $this->_helper->getPageTemplateProcessor());
    }
}
