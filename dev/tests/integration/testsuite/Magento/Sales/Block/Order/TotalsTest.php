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
namespace Magento\Sales\Block\Order;

class TotalsTest extends \PHPUnit_Framework_TestCase
{
    public function testToHtmlChildrenInitialized()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\State')->setAreaCode('frontend');

        /** @var $layout \Magento\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var \Magento\Sales\Block\Order\Totals $block */
        $block = $layout->createBlock('Magento\Sales\Block\Order\Totals', 'block');
        $block->setOrder(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order')
        )->setTemplate(
            'order/totals.phtml'
        );

        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\Element\Context');
        $childOne = $this->getMock('Magento\View\Element\Text', array('initTotals'), array($context));
        $childOne->expects($this->once())->method('initTotals');
        $layout->addBlock($childOne, 'child1', 'block');

        $childTwo = $this->getMock('Magento\View\Element\Text', array('initTotals'), array($context));
        $childTwo->expects($this->once())->method('initTotals');
        $layout->addBlock($childTwo, 'child2', 'block');

        $childThree = $this->getMock('Magento\View\Element\Text', array('initTotals'), array($context));
        $childThree->expects($this->once())->method('initTotals');
        $layout->addBlock($childThree, 'child3', 'block');

        $block->toHtml();
    }
}
