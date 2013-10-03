<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\AuthorizationInterface.
 */
namespace Magento;

class AuthorizationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Authorization model
     *
     * @var \Magento\AuthorizationInterface
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_policyMock;

    protected function setUp()
    {
        $this->_policyMock = $this->getMock('Magento\Authorization\Policy', array(), array(), '', false);
        $roleLocatorMock = $this->getMock('Magento\Authorization\RoleLocator', array(), array(), '', false);
        $roleLocatorMock->expects($this->any())->method('getAclRoleId')->will($this->returnValue('U1'));
        $this->_model = new \Magento\Authorization($this->_policyMock, $roleLocatorMock);
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
