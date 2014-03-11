<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Onepage;

class SuccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testGetAdditionalInfoHtml()
    {
        /** @var \Magento\Checkout\Block\Onepage\Success $block */
        $block = $this->objectManager->getObject('Magento\Checkout\Block\Onepage\Success');
        $layout = $this->getMock('Magento\View\LayoutInterface', [], [], '', false);
        $layout->expects($this->once())
            ->method('renderElement')
            ->with('order.success.additional.info')
            ->will($this->returnValue('AdditionalInfoHtml'));
        $block->setLayout($layout);
        $this->assertEquals('AdditionalInfoHtml', $block->getAdditionalInfoHtml());
    }
}
