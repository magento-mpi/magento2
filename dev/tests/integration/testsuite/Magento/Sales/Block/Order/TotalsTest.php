<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Block_Order_TotalsTest extends PHPUnit_Framework_TestCase
{
    public function testToHtmlChildrenInitialized()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        $block = $layout->createBlock('Magento_Sales_Block_Order_Totals', 'block');
        $block->setOrder(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order'))
            ->setTemplate('order/totals.phtml');

        $context = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Block_Context');
        $childOne = $this->getMock('Magento_Core_Block_Text', array('initTotals'), array($context));
        $childOne->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($childOne, 'child1', 'block');

        $childTwo = $this->getMock('Magento_Core_Block_Text', array('initTotals'), array($context));
        $childTwo->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($childTwo, 'child2', 'block');

        $childThree = $this->getMock('Magento_Core_Block_Text', array('initTotals'), array($context));
        $childThree->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($childThree, 'child3', 'block');

        $block->toHtml();
    }
}
