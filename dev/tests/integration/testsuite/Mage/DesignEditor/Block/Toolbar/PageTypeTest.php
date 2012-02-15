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
        $pageTypesFixture = __DIR__ . '/../../../Core/Model/Layout/_files/_page_types.xml';
        $this->_block = new Mage_DesignEditor_Block_Toolbar_PageType();
        $this->_block->setLayout(
            Mage_Core_Model_LayoutTest::getLayoutFromFixture($pageTypesFixture, $this)
        );
    }

    public function testRenderPageTypes()
    {
        $expected = __DIR__ . '/_files/_page_types_hierarchy.html';
        $actual = $this->_block->renderPageTypes();
        $this->assertXmlStringEqualsXmlFile($expected, $actual);
    }
}
