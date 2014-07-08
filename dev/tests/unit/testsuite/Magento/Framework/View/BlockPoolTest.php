<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for view BlockPool model
 */
namespace Magento\Framework\View;

class BlockPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockPool
     */
    protected $blockPool;

    /**
     * Block factory
     * @var \Magento\Framework\View\Element\BlockFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockFactory;

    protected function setUp()
    {
        $this->blockFactory = $this->getMockBuilder('Magento\Framework\View\Element\BlockFactory')
            ->disableOriginalConstructor()
            ->setMethods(['createBlock'])
            ->getMock();
        $this->blockPool = new BlockPool($this->blockFactory);
    }

    public function testAdd()
    {
        $blockName = 'testName';
        $blockClass = '\Magento\Framework\View\TestBlock';
        $arguments = ['key' => 'value'];

        $block = $this->getMock('Magento\Framework\View\TestBlock');

        $this->blockFactory->expects($this->atLeastOnce())
            ->method('createBlock')
            ->with($blockClass, $arguments)
            ->will($this->returnValue($block));

        $this->assertEquals($this->blockPool, $this->blockPool->add($blockName, $blockClass, $arguments));

        $this->assertEquals([$blockName => $block], $this->blockPool->get());
        $this->assertEquals($block, $this->blockPool->get($blockName));
        $this->assertNull($this->blockPool->get('someWrongName'));
    }
}

/**
 * Class TestBlock mock
 */
class TestBlock implements \Magento\Framework\View\Element\BlockInterface
{
    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml()
    {
        return '';
    }
}
