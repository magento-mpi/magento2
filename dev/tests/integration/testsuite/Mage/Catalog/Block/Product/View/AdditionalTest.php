<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Block_Product_View_AdditionalTest extends PHPUnit_Framework_TestCase
{
    public function testGetChildHtmlList()
    {
        $layout = new Mage_Core_Model_Layout;
        $block = new Mage_Catalog_Block_Product_View_Additional;
        $layout->addBlock($block, 'block');

        $child1 = $layout->addBlock('Mage_Core_Block_Text', 'child1', 'block');
        $expectedHtml1 = '<b>Any html of child1</b>';
        $child1->setText($expectedHtml1);

        $child2 = $layout->addBlock('Mage_Core_Block_Text', 'child2', 'block');
        $expectedHtml2 = '<b>Any html of child2</b>';
        $child2->setText($expectedHtml2);

        $list = $block->getChildHtmlList();

        $this->assertInternalType('array', $list);
        $this->assertCount(2, $list);
        $this->assertContains($expectedHtml1, $list);
        $this->assertContains($expectedHtml2, $list);
    }
}
