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
 * Test class for Mage_Webapi_Model_Authorization_Loader_Rule
 */
class Mage_Webapi_Model_Authorization_Loader_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Resource_Acl_Rule
     */
    protected $_resourceModelMock;

    /**
     * @var Mage_Webapi_Model_Authorization_Loader_Rule
     */
    protected $_model;

    /**
     * @var  Zend_Acl_Role
     */
    protected $_role;

    /**
     * @var Zend_Acl_Resource
     */
    protected $_resource;

    /**
     * @var Zend_Acl_Resource
     */
    protected $_resourceDeny;

    public function setUp()
    {
        $this->_resourceModelMock = $this->getMock('Mage_Webapi_Model_Resource_Acl_Rule',
            array('getRuleList'), array(), '', false);
        $this->_model = new Mage_Webapi_Model_Authorization_Loader_Rule(array(
            'resourceModel' => $this->_resourceModelMock,
        ));
        $this->_role = new Zend_Acl_Role(5);
        $this->_resource = new Zend_Acl_Resource('Mage_Customer::customer');
        $this->_resourceDeny = new Zend_Acl_Resource('Mage_Customer::customer_multiGet');
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Rule::populateAcl
     *
     * Test with existing rules
     */
    public function testPopulateAclWithRules()
    {
        $acl = new Magento_Acl();
        $acl->addRole($this->_role);
        $acl->deny($this->_role);
        $acl->addResource($this->_resource);
        $acl->addResource($this->_resourceDeny);
        $rules = array(array(
            'role_id' => $this->_role->getRoleId(),
            'resource_id' => $this->_resource->getResourceId()
        ));
        $this->_resourceModelMock->expects($this->once())->method('getRuleList')->will($this->returnValue($rules));
        $this->_model->populateAcl($acl);
        $this->assertTrue($acl->isAllowed($this->_role->getRoleId(), $this->_resource->getResourceId()));
        $this->assertFalse($acl->isAllowed($this->_role->getRoleId(), $this->_resourceDeny->getResourceId()));
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Rule::populateAcl
     *
     * Test with No existing rules
     */
    public function testPopulateAclWithNoRules()
    {
        $acl = new Magento_Acl();
        $acl->addRole($this->_role);
        $acl->deny($this->_role);
        $acl->addResource($this->_resource);
        $acl->addResource($this->_resourceDeny);
        $this->_resourceModelMock->expects($this->once())->method('getRuleList')->will($this->returnValue(array()));
        $this->_model->populateAcl($acl);
        $this->assertFalse($acl->isAllowed(5, 'Mage_Customer::customer'));
        $this->assertFalse($acl->isAllowed(5, 'Mage_Customer::customer_multiGet'));
    }
}
