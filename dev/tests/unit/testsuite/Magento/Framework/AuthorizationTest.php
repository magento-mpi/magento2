<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\AuthorizationInterface.
 */
namespace Magento\Framework;

class AuthorizationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Authorization model
     *
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_policyMock;

    protected function setUp()
    {
        $this->_policyMock = $this->getMock('Magento\Framework\Authorization\Policy', array(), array(), '', false);
        $roleLocatorMock = $this->getMock('Magento\Framework\Authorization\RoleLocator', array(), array(), '', false);
        $roleLocatorMock->expects($this->any())->method('getAclRoleId')->will($this->returnValue('U1'));
        $this->_model = new \Magento\Framework\Authorization($this->_policyMock, $roleLocatorMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testIsAllowedReturnPositiveValue()
    {
        $this->_policyMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->assertTrue($this->_model->isAllowed('Magento_Module::acl_resource'));
    }

    public function testIsAllowedReturnNegativeValue()
    {
        $this->_policyMock->expects($this->once())->method('isAllowed')->will($this->returnValue(false));
        $this->assertFalse($this->_model->isAllowed('Magento_Module::acl_resource'));
    }
}
