<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Authorization;

class RoleLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Authorization\RoleLocator
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock = array();

    protected function setUp()
    {
        $this->_sessionMock = $this->getMock(
            'Magento\Backend\Model\Auth\Session',
            array('getUser', 'getAclRole', 'hasUser'),
            array(),
            '',
            false
        );
        $this->_model = new \Magento\Backend\Model\Authorization\RoleLocator($this->_sessionMock);
    }

    public function testGetAclRoleIdReturnsCurrentUserAclRoleId()
    {
        $this->_sessionMock->expects($this->once())->method('hasUser')->will($this->returnValue(true));
        $this->_sessionMock->expects($this->once())->method('getUser')->will($this->returnSelf());
        $this->_sessionMock->expects($this->once())->method('getAclRole')->will($this->returnValue('some_role'));
        $this->assertEquals('some_role', $this->_model->getAclRoleId());
    }
}
