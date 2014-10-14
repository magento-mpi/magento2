<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Layout\ScheduledStructure;

use Magento\Framework\View\Layout;

/**
 * Class HelperTest
 * @covers Magento\Framework\View\Layout\ScheduledStructure\Helper
 */
class HelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $currentNodeName
     * @param string $actualNodeName
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $unsetPathElementCount
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $unsetStructureElementCount
     * @dataProvider providerScheduleStructure
     */
    public function testScheduleStructure(
        $currentNodeName, $actualNodeName, $unsetPathElementCount, $unsetStructureElementCount
    ) {
        $parentNodeName = 'parent_node';
        $currentNodeAs = 'currentNode';
        $after = 'after';
        $block = 'block';
        $testName = 'test_name';
        $data = [$testName => 1];
        $testPath = 'test_path';
        $potentialChild = 'potential_child';

        /** @var Layout\ScheduledStructure|\PHPUnit_Framework_MockObject_MockObject $scheduledStructure */
        $scheduledStructure = $this->getMock('Magento\Framework\View\Layout\ScheduledStructure', [], [], '', false);
        $scheduledStructure->expects($this->once())->method('hasPath')
            ->with($parentNodeName)
            ->will($this->returnValue(true));
        $scheduledStructure->expects($this->exactly(2))->method('hasStructureElement')
            ->with($actualNodeName)
            ->will($this->returnValue(true));
        $scheduledStructure->expects($this->once())->method('setPathElement')
            ->with($actualNodeName, $testPath . '/' . $actualNodeName)
            ->will($this->returnValue(true));
        $scheduledStructure->expects($this->once())->method('setStructureElement')
            ->with($actualNodeName, [$block, $currentNodeAs, $parentNodeName, $after, true, [$testName => 2]]);
        $scheduledStructure->expects($this->once())->method('getPath')
            ->with($parentNodeName)
            ->will($this->returnValue('test_path'));
        $scheduledStructure->expects($this->once())->method('getPaths')
            ->will($this->returnValue([$potentialChild => $testPath . '/' . $currentNodeName . '/']));
        $scheduledStructure->expects($unsetPathElementCount)->method('unsetPathElement')
            ->with($potentialChild);
        $scheduledStructure->expects($unsetStructureElementCount)->method('unsetStructureElement')
            ->with($potentialChild);
        $scheduledStructure->expects($this->once())->method('getStructureElement')
            ->with($actualNodeName)
            ->will(
                $this->returnValue([Layout\ScheduledStructure\Helper::SCHEDULED_STRUCTURE_INDEX_LAYOUT_DATA => $data]
            )
        );

        $currentNode = new Layout\Element(
            '<' . $block . ' name="' . $currentNodeName . '" as="' . $currentNodeAs . '" after="' . $after . '"/>'
        );
        $parentNode = new Layout\Element('<' . $block . ' name="' . $parentNodeName . '"/>');

        /** @var Layout\ScheduledStructure\Helper $helper */
        $helper = (new \Magento\TestFramework\Helper\ObjectManager($this))
            ->getObject('Magento\Framework\View\Layout\ScheduledStructure\Helper');
        $result = $helper->scheduleStructure($scheduledStructure, $currentNode, $parentNode, $data);
        $this->assertEquals($actualNodeName, $result);
    }

    /**
     * @return array
     */
    public function providerScheduleStructure()
    {
        return array(
            array('current_node', 'current_node', $this->once(), $this->once()),
            array('', 'parent_node_schedule_block1', $this->never(), $this->never())
        );
    }
}
