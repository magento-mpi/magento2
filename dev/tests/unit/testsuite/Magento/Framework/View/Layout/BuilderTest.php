<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Layout;

use Magento\Framework;

/**
 * Class BuilderTest
 * @covers \Magento\Framework\View\Layout\Builder
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'Magento\Framework\View\Layout\Builder';

    /**
     * @covers \Magento\Framework\View\Layout\Builder::build()
     */
    public function testBuild()
    {
        $fullActionName = 'route_controller_action';

        /** @var Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject */
        $request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $request->expects($this->exactly(3))->method('getFullActionName')->will($this->returnValue($fullActionName));

        /** @var Framework\View\Layout\ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject $processor */
        $processor = $this->getMock('Magento\Framework\View\Layout\ProcessorInterface', [], [], '', false);
        $processor->expects($this->once())->method('load');

        /** @var Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject */
        $layout = $this->getMock(
            'Magento\Framework\View\Layout',
            $this->getLayoutMockMethods(),
            [],
            '',
            false
        );
        $layout->expects($this->atLeastOnce())->method('getUpdate')->will($this->returnValue($processor));
        $layout->expects($this->atLeastOnce())->method('generateXml')->will($this->returnValue($processor));
        $layout->expects($this->atLeastOnce())->method('generateElements')->will($this->returnValue($processor));

        $data = ['full_action_name' => $fullActionName, 'layout' => $layout];
        $prefix = 'controller_action_layout_';
        /** @var Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject $eventManager */
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $eventManager->expects($this->at(0))->method('dispatch')->with($prefix . 'load_before', $data);
        $eventManager->expects($this->at(1))->method('dispatch')->with($prefix . 'generate_blocks_before', $data);
        $eventManager->expects($this->at(2))->method('dispatch')->with($prefix . 'generate_blocks_after', $data);
        $builder = $this->getBuilder(['eventManager' => $eventManager, 'request' => $request, 'layout' => $layout]);
        $builder->build();
    }

    /**
     * @return array
     */
    protected function getLayoutMockMethods()
    {
        return ['setBuilder', 'getUpdate', 'generateXml', 'generateElements'];
    }

    /**
     * @param array $arguments
     * @return \Magento\Framework\View\Layout\Builder
     */
    protected function getBuilder($arguments)
    {
        return (new \Magento\TestFramework\Helper\ObjectManager($this))->getObject(static::CLASS_NAME, $arguments);
    }
} 
