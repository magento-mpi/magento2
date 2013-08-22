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
 * Test class for Magento_AuthorizationInterface.
 */
class Magento_AuthorizationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Authorization model
     *
     * @var Magento_AuthorizationInterface
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_policyMock;

    public function setUp()
    {
        $this->_policyMock = $this->getMock('Magento_Authorization_Policy', array(), array(), '', false);
        $roleLocatorMock = $this->getMock('Magento_Authorization_RoleLocator', array(), array(), '', false);
        $roleLocatorMock->expects($this->any())->method('getAclRoleId')->will($this->returnValue('U1'));
        $this->_model = new Magento_Authorization($this->_policyMock, $roleLocatorMock);
    }

    public function tearDown()
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
