<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_TabsTest extends PHPUnit_Framework_TestCase
{
    public function testAddTab()
    {
        include(__DIR__ . '/_files/tab.php');
        $layout = new Mage_Core_Model_Layout;
        $block = $layout->createBlock('Mage_Adminhtml_Block_Widget_Tabs', 'block');
        $layout->addBlock('Mage_Adminhtml_Block_Widget_Tab_For_Testing', 'child_tab', 'block');
        $block->addTab('tab_id', 'child_tab');

        $this->assertEquals(array('tab_id'), $block->getTabsIds());
    }
}
