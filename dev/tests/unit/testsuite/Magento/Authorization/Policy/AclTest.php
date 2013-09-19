<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authorization\Policy;

class AclTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Authorization\Policy\Acl
     */
    protected $_model;

    protected $_aclMock;

    protected $_aclBuilderMock;

    public function setUp()
    {
        $this->_aclMock = $this->getMock('Magento\Acl');
        $this->_aclBuilderMock = $this->getMock('Magento\Acl\Builder', array(), array(), '', false);
        $this->_aclBuilderMock->expects($this->any())->method('getAcl')->will($this->returnValue($this->_aclMock));
        $this->_model = new \Magento\Authorization\Policy\Acl($this->_aclBuilderMock);
    }

    public function testIsAllowedReturnsTrueIfResourceIsAllowedToRole()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('some_role', 'some_resource')
            ->will($this->returnValue(true));

        $this->assertTrue($this->_model->isAllowed('some_role', 'some_resource'));
    }

    public function testIsAllowedReturnsFalseIfRoleDoesntExist()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('some_role', 'some_resource')
            ->will($this->throwException(new \Zend_Acl_Role_Registry_Exception));

        $this->_aclMock->expects($this->once())
            ->method('has')
            ->with('some_resource')
            ->will($this->returnValue(true));

        $this->assertFalse($this->_model->isAllowed('some_role', 'some_resource'));
    }

    public function testIsAllowedReturnsTrueIfResourceDoesntExistAndAllResourcesAreNotPermitted()
    {
        $this->_aclMock->expects($this->at(0))
            ->method('isAllowed')
            ->with('some_role', 'some_resource')
            ->will($this->throwException(new \Zend_Acl_Role_Registry_Exception));

        $this->_aclMock->expects($this->once())
            ->method('has')
            ->with('some_resource')
            ->will($this->returnValue(false));

        $this->_aclMock->expects($this->at(2))
            ->method('isAllowed')
            ->with('some_role', null)
            ->will($this->returnValue(true));

        $this->assertTrue($this->_model->isAllowed('some_role', 'some_resource'));
    }
}
