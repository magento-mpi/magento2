<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Block\Item\Price;

use Magento\Framework\Object;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tax\Block\Item\Price\Renderer
     */
    protected $renderer;

    /**
     * @var \Magento\Tax\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxHelper;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->taxHelper = $this->getMockBuilder('\Magento\Tax\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods([
                'displayCartPriceExclTax', 'displayCartBothPrices', 'displayCartPriceInclTax'
            ])
            ->getMock();

        $this->renderer = $objectManager->getObject(
            '\Magento\Tax\Block\Item\Price\Renderer',
            [
                'taxHelper' => $this->taxHelper,
            ]
        );
    }

    public function testDisplayPriceInclTax()
    {
        $this->taxHelper->expects($this->once())
            ->method('displayCartPriceInclTax');

        $this->renderer->displayPriceInclTax();
    }

    public function testDisplayPriceExclTax()
    {
        $this->taxHelper->expects($this->once())
            ->method('displayCartPriceExclTax');

        $this->renderer->displayPriceExclTax();
    }

    public function testDisplayBothPrices()
    {
        $this->taxHelper->expects($this->once())
            ->method('displayCartBothPrices');

        $this->renderer->displayBothPrices();
    }
}
