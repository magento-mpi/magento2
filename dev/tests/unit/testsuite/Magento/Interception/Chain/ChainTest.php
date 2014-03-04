<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Interception\Chain;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Interception\Chain\Chain
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pluginListMock;

    protected function setUp()
    {
        $this->_pluginListMock = $this->getMock('\Magento\Interception\PluginList', array(), array(), '', false);
        $this->_model = new \Magento\Interception\Chain\Chain($this->_pluginListMock);
    }

    /**
     * @covers  \Magento\Interception\Chain\Chain::invokeNext
     */
    public function testInvokeNextBeforePlugin()
    {
        $type = 'type';
        $method = 'method';

        $subjectMock = $this->getMock('SubjectClass', array('___callParent'));
        $pluginMock = $this->getMock('PluginClass', array('beforeMethod'));

        $pluginMock->expects($this->once())
            ->method('beforeMethod')
            ->with($subjectMock, 1, 2)
            ->will($this->returnValue('beforeMethodResult'));

        $this->_pluginListMock->expects($this->once())
            ->method('getNext')
            ->with($type, $method, null)
            ->will($this->returnValue(array(\Magento\Interception\Definition::LISTENER_BEFORE => array('code'))));

        $this->_pluginListMock->expects($this->once())
            ->method('getPlugin')
            ->with($type,'code')
            ->will($this->returnValue($pluginMock));

        $subjectMock->expects($this->once())
            ->method('___callParent')
            ->with('method', 'beforeMethodResult')
            ->will($this->returnValue('subjectMethodResult'));

        $this->assertEquals('subjectMethodResult', $this->_model->invokeNext($type, $method, $subjectMock, array(1,2)));
    }

    /**
     * @covers  \Magento\Interception\Chain\Chain::invokeNext
     */
    public function testInvokeNextAroundPlugin()
    {
        $type = 'type';
        $method = 'method';

        $subjectMock = $this->getMock('SubjectClass');
        $pluginMock = $this->getMock('PluginClass', array('aroundMethod'));

        $pluginMock->expects($this->once())
            ->method('aroundMethod')
            ->with($this->anything())
            ->will($this->returnValue('subjectMethodResult'));

        $this->_pluginListMock->expects($this->once())
            ->method('getNext')
            ->with($type, $method, null)
            ->will($this->returnValue(array(\Magento\Interception\Definition::LISTENER_AROUND => 'code')));

        $this->_pluginListMock->expects($this->once())
            ->method('getPlugin')
            ->with($type,'code')
            ->will($this->returnValue($pluginMock));

        $this->assertEquals('subjectMethodResult', $this->_model->invokeNext($type, $method, $subjectMock, array()));
    }

    /**
     * @covers  \Magento\Interception\Chain\Chain::invokeNext
     */
    public function testInvokeNextAfterPlugin()
    {
        $type = 'type';
        $method = 'method';

        $subjectMock = $this->getMock('SubjectClass', array('___callParent'));
        $pluginMock = $this->getMock('PluginClass', array('afterMethod'));

        $pluginMock->expects($this->once())
            ->method('afterMethod')
            ->with($subjectMock, 'subjectMethodResult')
            ->will($this->returnValue('afterMethodResult'));

        $this->_pluginListMock->expects($this->once())
            ->method('getNext')
            ->with($type, $method, null)
            ->will($this->returnValue(array(\Magento\Interception\Definition::LISTENER_AFTER => array('code'))));

        $this->_pluginListMock->expects($this->once())
            ->method('getPlugin')
            ->with($type,'code')
            ->will($this->returnValue($pluginMock));

        $subjectMock->expects($this->once())
            ->method('___callParent')
            ->with('method', array(1,2))
            ->will($this->returnValue('subjectMethodResult'));

        $this->assertEquals('afterMethodResult', $this->_model->invokeNext($type, $method, $subjectMock, array(1,2)));
    }
}
