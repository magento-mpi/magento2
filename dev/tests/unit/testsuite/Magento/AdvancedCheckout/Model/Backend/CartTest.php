<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Model\Backend;

/**
 * Test class for \Magento\AdvancedCheckout\Model\Backend\Cart
 */
class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testGetActualQuote()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $quote = $this->getMock('Magento\Sales\Model\Quote', ['getQuote', '__wakeup'], [], '', false);
        $quote->expects($this->once())->method('getQuote')->will($this->returnValue('some value'));
        /** @var Cart $model */
        $model = $helper->getObject('Magento\AdvancedCheckout\Model\Backend\Cart');
        $model->setQuote($quote);
        $this->assertEquals('some value', $model->getActualQuote());
    }
}
