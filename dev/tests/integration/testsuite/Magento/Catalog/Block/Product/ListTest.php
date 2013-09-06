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
 * Test class for Magento_Catalog_Block_Product_List.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class Magento_Catalog_Block_Product_ListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Block_Product_List
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Catalog_Block_Product_List');
    }

    public function testGetLayer()
    {
        $this->assertInstanceOf('Magento_Catalog_Model_Layer', $this->_block->getLayer());
    }

    public function testGetLoadedProductCollection()
    {
        $this->_block->setShowRootCategory(true);
        $collection = $this->_block->getLoadedProductCollection();
        $this->assertInstanceOf(
            'Magento_Catalog_Model_Resource_Product_Collection',
            $collection
        );
        /* Check that root category was defined for Layer as current */
        $this->assertEquals(2, $this->_block->getLayer()->getCurrentCategory()->getId());
    }

    /**
     * @covers Magento_Catalog_Block_Product_List::getToolbarBlock
     * @covers Magento_Catalog_Block_Product_List::getMode
     * @covers Magento_Catalog_Block_Product_List::getToolbarHtml
     * @covers Magento_Catalog_Block_Product_List::toHtml
     */
    public function testToolbarCoverage()
    {
        /** @var $parent Magento_Catalog_Block_Product_List */
        $parent = $this->_getLayout()->createBlock('Magento_Catalog_Block_Product_List', 'parent');

        /* Prepare toolbar block */
        $toolbar = $parent->getToolbarBlock();
        $this->assertInstanceOf('Magento_Catalog_Block_Product_ProductList_Toolbar', $toolbar, 'Default Toolbar');

        $parent->setChild('toolbar', $toolbar);
        /* In order to initialize toolbar collection block toHtml should be called before toolbar toHtml */
        $this->assertEmpty($parent->toHtml(), 'Block HTML'); /* Template not specified */
        $this->assertEquals('grid', $parent->getMode(), 'Default Mode'); /* default mode */
        $this->assertNotEmpty($parent->getToolbarHtml(), 'Toolbar HTML'); /* toolbar for one simple product */
    }


    public function testGetAdditionalHtmlEmpty()
    {
        $this->_block->setLayout($this->_getLayout());
        $this->assertEmpty($this->_block->getAdditionalHtml());
    }

    public function testGetAdditionalHtml()
    {
        $layout = $this->_getLayout();
        /** @var $parent Magento_Catalog_Block_Product_List */
        $parent = $layout->createBlock('Magento_Catalog_Block_Product_List');
        $childBlock = $layout->createBlock('Magento_Core_Block_Text', 'test', array('data' => array('text' => 'test')));
        $layout->setChild($parent->getNameInLayout(), $childBlock->getNameInLayout(), 'additional');
        $this->assertEquals('test', $parent->getAdditionalHtml());
    }

    public function testSetCollection()
    {
        $this->_block->setCollection('test');
        $this->assertEquals('test', $this->_block->getLoadedProductCollection());
    }

    public function testGetPriceBlockTemplate()
    {
        $this->assertNull($this->_block->getPriceBlockTemplate());
        $this->_block->setData('price_block_template', 'test');
        $this->assertEquals('test', $this->_block->getPriceBlockTemplate());
    }

    public function testPrepareSortableFieldsByCategory()
    {
        /** @var $category Magento_Catalog_Model_Category */
        $category = Mage::getModel('Magento_Catalog_Model_Category');
        $category->setDefaultSortBy('name');
        $this->_block->prepareSortableFieldsByCategory($category);
        $this->assertEquals('name', $this->_block->getSortBy());
    }

    protected function _getLayout()
    {
        return Mage::app()->getLayout();
    }
}
