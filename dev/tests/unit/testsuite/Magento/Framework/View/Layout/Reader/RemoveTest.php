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
        $xml = '<?xml version="1.0"?>
<!--
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
<page>
    <head>
        <link src="Magento_Backend::js/bootstrap/editor.js"/>
        <css src="prototype/windows/themes/default.css"/>
        <css src="Magento_Core::prototype/magento.css"/>
    </head>
</page>';
        $this->context = $this->getMockBuilder('Magento\Framework\View\Layout\Reader\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->element =  new \Magento\Framework\View\Layout\Element($xml);
        $this->scheduledStructure = $this->getMockBuilder('Magento\Framework\View\Layout\ScheduledStructure')
            ->disableOriginalConstructor()->setMethods(['setElementToRemoveList', '__wakeup'])
            ->getMock();
        $this->model = new Remove;
    }

    public function testGetSupportedNodes()
    {
        $data[] = 'remove';
        $this->assertEquals($data, $this->model->getSupportedNodes());
    }

    public function testProcess()
    {
        $this->context->expects($this->any())
            ->method('getScheduledStructure')
            ->will($this->returnValue($this->scheduledStructure));
        $this->assertFalse($this->model->process($this->context, $this->element, $this->element));
    }
}
