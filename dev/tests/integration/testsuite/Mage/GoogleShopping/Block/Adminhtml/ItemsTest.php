<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_GoogleShopping
 */
class Mage_GoogleShopping_Block_Adminhtml_ItemsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Isolation enabled because of contaminating Mage::getDesign()
     *
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        $this->markTestIncomplete('Mage_GoogleShopping is not implemented yet');

        $block = new Mage_GoogleShopping_Block_Adminhtml_Items;
        Mage::getDesign()->setDesignTheme('default/default/default', 'adminhtml');
        $layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $layout->addBlock($block, 'items');
        $expected = uniqid();
        $child = new Mage_Core_Block_Text(array('id' => $expected));
        $layout->addBlock($child, 'product', 'items');
        $this->assertContains('$(\'' . $expected . '\');', $block->toHtml());
    }
}
