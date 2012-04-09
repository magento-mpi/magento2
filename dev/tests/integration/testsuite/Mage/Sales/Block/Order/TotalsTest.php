<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Sales
 */
class Mage_Sales_Block_Order_TotalsTest extends PHPUnit_Framework_TestCase
{
    public function testToHtmlChildrenInitialized()
    {
        $block = new Mage_Sales_Block_Order_Totals;
        $block->setOrder(new Mage_Sales_Model_Order)
            ->setTemplate('order/totals.phtml');
        $layout = new Mage_Core_Model_Layout;
        $layout->addBlock($block, 'block');

        $child1 = $this->getMock('Mage_Core_Block_Text', array('initTotals'));
        $child1->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($child1, 'child1', 'block');

        $layout->addBlock('Mage_Core_Block_Text', 'child2', 'block');

        $child3 = $this->getMock('Mage_Core_Block_Text', array('initTotals'));
        $child3->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($child3, 'child3', 'block');

        $block->toHtml();
    }
}
