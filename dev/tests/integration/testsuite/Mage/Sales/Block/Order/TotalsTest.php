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

class Mage_Sales_Block_Order_TotalsTest extends PHPUnit_Framework_TestCase
{
    public function testToHtmlChildrenInitialized()
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $block = $layout->createBlock('Mage_Sales_Block_Order_Totals', 'block');
        $block->setOrder(Mage::getModel('Mage_Sales_Model_Order'))
            ->setTemplate('order/totals.phtml');

        $context = Mage::getSingleton('Mage_Core_Block_Context');
        $childOne = $this->getMock('Mage_Core_Block_Text', array('initTotals'), array($context));
        $childOne->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($childOne, 'child1', 'block');

        $childTwo = $this->getMock('Mage_Core_Block_Text', array('initTotals'), array($context));
        $childTwo->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($childTwo, 'child2', 'block');

        $childThree = $this->getMock('Mage_Core_Block_Text', array('initTotals'), array($context));
        $childThree->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($childThree, 'child3', 'block');

        $block->toHtml();
    }
}
