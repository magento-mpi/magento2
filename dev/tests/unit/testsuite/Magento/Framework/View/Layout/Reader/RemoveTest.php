<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\View\Layout\Reader\Remove
 */
namespace Magento\Framework\View\Layout\Reader;

class RemoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Layout\Reader\Remove
     */
    protected $model;

    /**
     * @var \Magento\Framework\View\Layout\Reader\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\View\Layout\Element
     */
    protected $element;

    /**
     * @var \Magento\Framework\View\Layout\ScheduledStructure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scheduledStructure;

    public function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Framework\View\Layout\Reader\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->scheduledStructure = $this->getMockBuilder('Magento\Framework\View\Layout\ScheduledStructure')
            ->disableOriginalConstructor()->setMethods(['setElementToRemoveList', '__wakeup'])
            ->getMock();
        $this->model = new Remove;
    }

    public function testGetSupportedNodes()
    {
        $data[] = \Magento\Framework\View\Layout\Reader\Remove::TYPE_REMOVE;
        $this->assertEquals($data, $this->model->getSupportedNodes());
    }

    /**
     * @dataProvider processDataProvider
     */
    public function testProcess($xml)
    {
        $this->element = new \Magento\Framework\View\Layout\Element($xml);
        $this->context->expects($this->any())
            ->method('getScheduledStructure')
            ->will($this->returnValue($this->scheduledStructure));
        $this->scheduledStructure->expects($this->once())->method('setElementToRemoveList')->with(
            (string)$this->element->getAttribute('name')
        );
        $this->model->process($this->context, $this->element, $this->element);
    }

    public function processDataProvider()
    {
        return [
            [
                '<?xml version="1.0"?>
<page>
    <body>
        <remove name="header"/>
        <remove name="menu"/>
    </body>
</page>'
            ]
        ];
    }
}
