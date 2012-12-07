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
 * Test Webapi Global ACL Role model
 */
class  Mage_Webapi_Model_Acl_Global_RoleTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test get config node identifiers
     *
     * @return string
     */
    public function testGetConfigNodeName()
    {
        $this->getModelMockBuilder('Mage_Webapi_Model_Acl_Global_Role')
            ->setMethods(array('getId'))
            ->getMock()
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(Mage_Webapi_Model_Acl_Global_Role::ROLE_GUEST_ID));

        /* @var $role Mage_Webapi_Model_Acl_Global_Role */
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Global_Role');
        $this->assertEquals($role->getConfigNodeName(), Mage_Webapi_Model_Acl_Global_Role::ROLE_CONFIG_NODE_NAME_GUEST);

        $this->getModelMockBuilder('Mage_Webapi_Model_Acl_Global_Role')
            ->setMethods(array('getId'))
            ->getMock()
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(Mage_Webapi_Model_Acl_Global_Role::ROLE_CUSTOMER_ID));
        /* @var $role Mage_Webapi_Model_Acl_Global_Role */
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Global_Role');
        $this->assertEquals($role->getConfigNodeName(),
            Mage_Webapi_Model_Acl_Global_Role::ROLE_CONFIG_NODE_NAME_CUSTOMER);

        $this->getModelMockBuilder('Mage_Webapi_Model_Acl_Global_Role')
            ->setMethods(array('getId'))
            ->getMock()
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));
        /* @var $role Mage_Webapi_Model_Acl_Global_Role */
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Global_Role');
        $this->assertEquals($role->getConfigNodeName(), Mage_Webapi_Model_Acl_Global_Role::ROLE_CONFIG_NODE_NAME_ADMIN);
    }
}
