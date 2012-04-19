<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test API2 Global ACL Role model
 */
class  Mage_Api2_Model_Acl_Global_RoleTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test get config node identifiers
     *
     * @return string
     */
    public function testGetConfigNodeName()
    {
        $this->getModelMockBuilder('api2/acl_global_role')
            ->setMethods(array('getId'))
            ->getMock()
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(Mage_Api2_Model_Acl_Global_Role::ROLE_GUEST_ID));

        /* @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $this->assertEquals($role->getConfigNodeName(), Mage_Api2_Model_Acl_Global_Role::ROLE_CONFIG_NODE_NAME_GUEST);

        $this->getModelMockBuilder('api2/acl_global_role')
            ->setMethods(array('getId'))
            ->getMock()
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID));
        /* @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $this->assertEquals($role->getConfigNodeName(),
            Mage_Api2_Model_Acl_Global_Role::ROLE_CONFIG_NODE_NAME_CUSTOMER);

        $this->getModelMockBuilder('api2/acl_global_role')
            ->setMethods(array('getId'))
            ->getMock()
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));
        /* @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $this->assertEquals($role->getConfigNodeName(), Mage_Api2_Model_Acl_Global_Role::ROLE_CONFIG_NODE_NAME_ADMIN);
    }
}
