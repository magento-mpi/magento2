<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Webapi_Model_Authorization_Loader_Role
 */
class Mage_Webapi_Model_Authorization_Loader_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Resource_Acl_Role
     */
    protected $_resourceModelMock;

    /**
     * @var Mage_Webapi_Model_Authorization_Loader_Role
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    public function setUp()
    {
        $this->_resourceModelMock = $this->getMock('Mage_Webapi_Model_Resource_Acl_Role',
            array('getRolesIds'), array(), '', false);
        $this->_config = $this->getMock('Mage_Core_Model_Config',
            array('getModelInstance'), array(), '', false);
        $this->_model = new Mage_Webapi_Model_Authorization_Loader_Role(array(
            'resourceModel' => $this->_resourceModelMock,
            'config' => $this->_config,
        ));
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Role::populateAcl
     *
     * Test with existing role Ids
     */
    public function testPopulateAclWithRoles()
    {
        $roleMap = array(array('role_id' => 2), array('role_id' => 4), array('role_id' => 5), array('role_id' => 8));
        $roleIds = array(2, 4, 5, 8);
        $getModel = function($className, $roleId)
        {
            return new Mage_Webapi_Model_Authorization_Role($roleId);
        };
        $this->_resourceModelMock->expects($this->once())->method('getRolesIds')->will($this->returnValue($roleMap));
        $this->_config->expects($this->any())->method('getModelInstance')->will($this->returnCallback($getModel));
        $acl = new Magento_Acl();
        $this->_model->populateAcl($acl);
        $this->assertEquals($roleIds, $acl->getRoles());
        //Check that nothing is allowed for just loaded roles
        foreach ($roleIds as $role) {
            $this->assertFalse($acl->isAllowed($role));
        }
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Role::populateAcl
     *
     * Test with No existing role Ids
     */
    public function testPopulateAclWithNoRoles()
    {
        $this->_resourceModelMock->expects($this->once())->method('getRolesIds')->will($this->returnValue(array()));
        $acl = new Magento_Acl();
        $this->_model->populateAcl($acl);
        $this->assertEquals(array(), $acl->getRoles());
    }
}
