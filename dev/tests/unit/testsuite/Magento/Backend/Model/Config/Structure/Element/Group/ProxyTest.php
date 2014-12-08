<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Structure\Element\Group;

class ProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Structure\Element\Group\Proxy
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->_model = new \Magento\Backend\Model\Config\Structure\Element\Group\Proxy($this->_objectManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_objectManagerMock);
    }

    public function testProxyInitializesProxiedObjectOnFirstCall()
    {
        $groupMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Group',
            [],
            [],
            '',
            false
        );

        $groupMock->expects($this->once())->method('setData');
        $groupMock->expects($this->once())->method('getId')->will($this->returnValue('group_id'));
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Backend\Model\Config\Structure\Element\Group'
        )->will(
            $this->returnValue($groupMock)
        );

        $this->_model->setData([], '');
        $this->assertEquals('group_id', $this->_model->getId());
    }
}
