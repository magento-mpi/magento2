<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Generator;

/**
 * @covers Magento\Framework\View\Layout\Generator\Block
 */
class BlockTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $elementName = 'test_block';
        $scheduleStructure = $this->getMock('Magento\Framework\View\Layout\ScheduledStructure', [], [], '', false);
        $scheduleStructure->expects($this->once())->method('getElements')->will(
            $this->returnValue(
                [
                    $elementName => [
                        'block',
                        [
                            'actions' => [
                                [
                                    'method_name',
                                    [
                                        'test_argument' => ['argument_data']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        );

        $testGroup = 'test_group';
        $class = 'test_class';
        $scheduleStructure->expects($this->once())->method('getElement')->with($elementName)->will(
            $this->returnValue(
                [
                    '',
                    [
                        'attributes' => [
                            'class' => $class,
                            'template' => 'test_template',
                            'ttl' => 'test_ttl',
                            'group' => $testGroup
                        ],
                        'arguments' => ['data' => ['argument_data']]
                    ]
                ]
            )
        );
        $scheduleStructure->expects($this->once())->method('unsetElement')->with('test_block');

        /** @var \Magento\Framework\View\Layout\Reader\Context|\PHPUnit_Framework_MockObject_MockObject $readerContext */
        $readerContext = $this->getMock('Magento\Framework\View\Layout\Reader\Context', [], [], '', false);
        $readerContext->expects($this->once())->method('getScheduledStructure')
            ->will($this->returnValue($scheduleStructure));

        $layout = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);

        /** @var \Magento\Framework\View\Element\AbstractBlock|\PHPUnit_Framework_MockObject_MockObject $blockInstance */
        $blockInstance = $this->getMock('Magento\Framework\View\Element\AbstractBlock', [], [], '', false);
        $blockInstance->expects($this->once())->method('setType')->with(get_class($blockInstance));
        $blockInstance->expects($this->once())->method('setNameInLayout')->with($elementName);
        $blockInstance->expects($this->once())->method('addData')->with(['data' => null]);
        $blockInstance->expects($this->once())->method('setTemplate')->with('test_template');
        $blockInstance->expects($this->once())->method('setTtl')->with(0);
        $blockInstance->expects($this->once())->method('setLayout')->with($layout);
        $blockInstance->expects($this->once())->method('method_name')->with(null);

        $layout->expects($this->once())->method('setBlock')->with('test_block', $blockInstance);

        $structure = $this->getMock('Magento\Framework\View\Layout\Data\Structure', [], [], '', false);
        $structure->expects($this->once())->method('addToParentGroup')->with($elementName, $testGroup);

        /** @var \Magento\Framework\View\Layout\Generator\Context|\PHPUnit_Framework_MockObject_MockObject $generatorContext */
        $generatorContext = $this->getMock('Magento\Framework\View\Layout\Generator\Context', [], [], '', false);
        $generatorContext->expects($this->once())->method('getLayout')->will($this->returnValue($layout));
        $generatorContext->expects($this->once())->method('getStructure')->will($this->returnValue($structure));

        /** @var \Magento\Framework\Data\Argument\InterpreterInterface|\PHPUnit_Framework_MockObject_MockObject $argumentInterpreter */
        $argumentInterpreter = $this->getMock('Magento\Framework\Data\Argument\InterpreterInterface', [], [], '', false);
        $argumentInterpreter->expects($this->exactly(2))->method('evaluate')->with(['argument_data']);


        /** @var \Magento\Framework\View\Element\BlockFactory|\PHPUnit_Framework_MockObject_MockObject $blockFactory */
        $blockFactory = $this->getMock('Magento\Framework\View\Element\BlockFactory', [], [], '', false);
        $blockFactory->expects($this->once())->method('createBlock')->with($class, ['data' => ['data' => null]])
            ->will($this->returnValue($blockInstance));

        /** @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject $eventManager */
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $eventManager->expects($this->once())->method('dispatch')->with('core_layout_block_create_after', ['block' => $blockInstance]);

        /** @var \Magento\Framework\View\Layout\Generator\Block $block */
        $block = (new \Magento\TestFramework\Helper\ObjectManager($this))
            ->getObject(
                'Magento\Framework\View\Layout\Generator\Block',
                [
                    'argumentInterpreter' => $argumentInterpreter,
                    'blockFactory' => $blockFactory,
                    'eventManager' => $eventManager
                ]
            );
        $block->process($readerContext, $generatorContext);
    }
}