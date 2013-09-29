<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Button;

class SplitTest extends \PHPUnit_Framework_TestCase
{
    public function testHasSplit()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Backend\Block\Widget\Button\SplitButton $block */
        $block = $objectManagerHelper->getObject('Magento\Backend\Block\Widget\Button\SplitButton');
        $this->assertSame(true, $block->hasSplit());
        $block->setData('has_split', false);
        $this->assertSame(false, $block->hasSplit());
        $block->setData('has_split', true);
        $this->assertSame(true, $block->hasSplit());
    }
}
