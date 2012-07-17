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

class Mage_Catalog_Helper_MapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Helper_Map
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new Mage_Catalog_Helper_Map;
    }

    protected function tearDown()
    {
        $this->_helper = null;
    }

    public function testGetCategoryUrl()
    {
        $this->assertStringEndsWith('/catalog/seo_sitemap/category/', $this->_helper->getCategoryUrl());
    }

    public function testGetProductUrl()
    {
        $this->assertStringEndsWith('/catalog/seo_sitemap/product/', $this->_helper->getProductUrl());
    }

    public function testGetIsUseCategoryTreeModeDefault()
    {
        $this->assertFalse($this->_helper->getIsUseCategoryTreeMode());
    }

    /**
     * @magentoConfigFixture current_store catalog/sitemap/tree_mode 1
     */
    public function testGetIsUseCategoryTreeMode()
    {
        $this->assertTrue($this->_helper->getIsUseCategoryTreeMode());
    }
}
