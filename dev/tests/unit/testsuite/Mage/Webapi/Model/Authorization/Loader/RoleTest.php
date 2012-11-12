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
     * @var Mage_Webapi_Model_Acl_RoleFactory
     */
    protected $_roleFactory;

    /**
     * @var Mage_Webapi_Model_Acl_Role
     */
    protected $_role;

    /**
     * @var Magento_Acl
     */
    protected $_acl;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_resourceModelMock = $this->getMock('Mage_Webapi_Model_Resource_Acl_Role',
            array('getRolesIds'), array(), '', false);

        $this->_role = new Mage_Webapi_Model_Authorization_Role(5);

        $this->_roleFactory = $this->getMock('Mage_Webapi_Model_Authorization_RoleFactory',
            array('createRole'), array(), '', false);

        $this->_acl = $this->getMock('Magento_Acl', array('addRole', 'deny'), array(), '',
            false);

        $this->_model = $helper->getModel('Mage_Webapi_Model_Authorization_Loader_Role', array(
            'roleResource' => $this->_resourceModelMock,
            'roleFactory' => $this->_roleFactory,
        ));
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Role::populateAcl
     *
     * Test with existing role Ids
     */
    public function testPopulateAclWithRoles()
    {
        $roleIds = array(2, 4);

        $this->_resourceModelMock->expects($this->once())->method('getRolesIds')->will($this->returnValue($roleIds));

        $this->_roleFactory->expects($this->any())->method('createRole')->will($this->returnValue($this->_role));

        $this->_acl->expects($this->exactly(2))
            ->method('addRole')
            ->with($this->_role)
            ->will($this->returnValue(null));

        $this->_acl->expects($this->exactly(2))
            ->method('deny')
            ->with($this->_role)
            ->will($this->returnValue(null));

        $this->_model->populateAcl($this->_acl);

    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Role::populateAcl
     *
     * Test with No existing role Ids
     */
    public function testPopulateAclWithNoRoles()
    {
        $this->_resourceModelMock->expects($this->once())->method('getRolesIds')->will($this->returnValue(array()));
        $this->_model->populateAcl($this->_acl);
        $this->_acl->expects($this->never())
            ->method('addRole');
    }
}
