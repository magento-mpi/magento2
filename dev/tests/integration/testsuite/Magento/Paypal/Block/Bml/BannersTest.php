<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Bml;

use Magento\TestFramework\Helper\Bootstrap;

class BannersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int $publisherId
     * @param int $display
     * @param int $position
     * @param int $configPosition
     * @param bool $isEmptyHtml
     * @dataProvider testToHtmlDataProvider
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     */
    public function testToHtml($publisherId, $display, $position, $configPosition, $isEmptyHtml)
    {
        $paypalConfig = $this->getMock('Magento\Paypal\Model\Config', [], [], '', false);
        $paypalConfig->expects($this->any())->method('getBmlPublisherId')->will($this->returnValue($publisherId));
        $paypalConfig->expects($this->any())->method('getBmlDisplay')->will($this->returnValue($display));
        $paypalConfig->expects($this->any())->method('getBmlPosition')->will($this->returnValue($configPosition));

        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = Bootstrap::getObjectManager()->get('Magento\Framework\View\LayoutInterface');
        $block = $layout->createBlock(
            'Magento\Paypal\Block\Bml\Banners',
            '',
            [
                'paypalConfig' => $paypalConfig,
                'data' => ['position' => $position]
            ]
        );
        $block->setTemplate('bml.phtml');
        $html = $block->toHtml();

        if ($isEmptyHtml) {
            $this->assertEmpty($html);
        } else {
            $this->assertContains('data-pp-pubid="' . $block->getPublisherId() . '"', $html);
            $this->assertContains('data-pp-placementtype="' . $block->getSize() . '"', $html);
        }
    }

    /**
     * @return array
     */
    public function testToHtmlDataProvider()
    {
        return [
            [1, 1, 100, 100, false],
            [0, 1, 100, 100, true],
            [1, 0, 100, 100, true],
            [1, 0, 10, 100, true]
        ];
    }
}
