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
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getModel('\Magento\Core\Model\Layout');
        $block = $layout->createBlock('\Magento\Sales\Block\Order\Totals', 'block');
        $block->setOrder(Mage::getModel('\Magento\Sales\Model\Order'))
            ->setTemplate('order/totals.phtml');

        $context = Mage::getSingleton('Magento\Core\Block\Context');
        $childOne = $this->getMock('Magento\Core\Block\Text', array('initTotals'), array($context));
        $childOne->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($childOne, 'child1', 'block');

        $childTwo = $this->getMock('Magento\Core\Block\Text', array('initTotals'), array($context));
        $childTwo->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($childTwo, 'child2', 'block');

        $childThree = $this->getMock('Magento\Core\Block\Text', array('initTotals'), array($context));
        $childThree->expects($this->once())
            ->method('initTotals');
        $layout->addBlock($childThree, 'child3', 'block');

        $block->toHtml();
    }
}
