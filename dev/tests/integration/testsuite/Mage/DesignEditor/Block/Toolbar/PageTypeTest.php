<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Toolbar_PageTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar_PageType
     */
    protected $_block;

    protected function setUp()
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $pageTypesFixture = __DIR__ . '/../../../Core/Model/Layout/_files/_page_types.xml';
        $this->_block = new Mage_DesignEditor_Block_Toolbar_PageType();
        $this->_block->setLayout($layoutUtility->getLayoutFromFixture($pageTypesFixture));
    }

    public function testRenderHierarchy()
    {
        $expected = __DIR__ . '/_files/_page_types_hierarchy.html';
        $actual = $this->_block->renderHierarchy();
        $this->assertXmlStringEqualsXmlFile($expected, $actual);
    }

    public function testGetSelectedItemFromPageHandles()
    {
        $this->_block->getLayout()->getUpdate()->addPageHandles(array('catalog_product_view_type_simple'));
        $this->assertEquals('catalog_product_view_type_simple', $this->_block->getSelectedItem());
    }

    public function testGetSelectedItemFromHandles()
    {
        $this->_block->getLayout()->getUpdate()->addHandle(array(
            'catalog_product_view',
            'catalog_product_view_type_grouped',
            'not_a_page_type',
        ));
        $this->assertEquals('catalog_product_view_type_grouped', $this->_block->getSelectedItem());
    }

    public function testGetSelectedItemLabel()
    {
        $this->assertNull($this->_block->getSelectedItemLabel());
        $this->_block->setSelectedItem('default');
        $this->assertEquals('All Pages', $this->_block->getSelectedItemLabel());
    }

    public function testSetSelectedPageItem()
    {
        $this->assertFalse($this->_block->getSelectedItem());
        $this->_block->setSelectedItem('catalog_product_view_type_configurable');
        $this->assertEquals('catalog_product_view_type_configurable', $this->_block->getSelectedItem());
    }
}
