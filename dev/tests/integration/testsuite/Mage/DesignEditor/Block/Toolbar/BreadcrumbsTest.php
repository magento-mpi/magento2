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
class Mage_DesignEditor_Block_Toolbar_BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar_Breadcrumbs
     */
    protected $_block;

    protected function setUp()
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $pageTypesFixture = __DIR__ . '/../../../Core/Model/Layout/_files/_page_types.xml';
        $layout = $layoutUtility->getLayoutFromFixture($pageTypesFixture);
        $layout->getUpdate()->addPageHandles(array('PRODUCT_TYPE_simple'));
        $this->_block = new Mage_DesignEditor_Block_Toolbar_Breadcrumbs(
            array('template' => 'toolbar/breadcrumbs.phtml')
        );
        $this->_block->setLayout($layout);
    }

    public function testGetBreadcrumbs()
    {
        $expected = require(__DIR__ . '/_files/_breadcrumbs_simple_product.php');
        $actual = $this->_block->getBreadcrumbs();
        $this->assertEquals($expected, $actual);
    }

    public function testToHtml()
    {
        $expected = __DIR__ . '/_files/_breadcrumbs_simple_product.html';
        $actual = $this->_block->toHtml();
        $this->assertXmlStringEqualsXmlFile($expected, $actual);
    }
}
