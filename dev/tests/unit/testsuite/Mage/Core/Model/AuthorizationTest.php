<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Authorization.
 */
class Mage_Core_Model_AuthorizationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Authorization model
     *
     * @var Mage_Core_Model_Authorization
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

        $data = array(
            'policy' => $this->_policyMock,
            'roleLocator' => $roleLocatorMock
        );
        $this->_model = new Mage_Core_Model_Authorization($data);
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    public function testIsAllowedReturnPositiveValue()
    {
        $this->_policyMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->assertTrue($this->_model->isAllowed('Mage_Module::acl_resource'));
    }

    public function testIsAllowedReturnNegativeValue()
    {
        $this->_policyMock->expects($this->once())->method('isAllowed')->will($this->returnValue(false));
        $this->assertFalse($this->_model->isAllowed('Mage_Module::acl_resource'));
    }
}
