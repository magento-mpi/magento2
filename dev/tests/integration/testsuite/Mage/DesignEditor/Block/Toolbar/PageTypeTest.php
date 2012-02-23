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

/**
 * @group module:Mage_DesignEditor
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
        $layout = $layoutUtility->getLayoutFromFixture($pageTypesFixture);
        $layout->getUpdate()->addPageHandles(array('PRODUCT_TYPE_simple'));
        $this->_block = new Mage_DesignEditor_Block_Toolbar_PageType();
        $this->_block->setLayout($layout);
    }

    public function testRenderPageTypes()
    {
        $expected = __DIR__ . '/_files/_page_types_hierarchy.html';
        $actual = $this->_block->renderPageTypes();
        $this->assertXmlStringEqualsXmlFile($expected, $actual);
    }

    public function testGetSelectedPageType()
    {
        $this->assertEquals('PRODUCT_TYPE_simple', $this->_block->getSelectedPageType());
        $this->_block->setSelectedPageType('PRODUCT_TYPE_configurable');
        $this->assertEquals('PRODUCT_TYPE_configurable', $this->_block->getSelectedPageType());
    }
}
