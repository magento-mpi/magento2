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
     * @var int
     */
    protected $_roleId = 5;

    /**
     * @var string
     */
    protected $_resourceId = 'customer/get';

    /**
     * @var string
     */
    protected $_resourceDenyId = 'customer/delete';

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

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_resourceModelMock = $this->getMock('Mage_Webapi_Model_Resource_Acl_Rule',
            array('getRuleList'), array(), '', false);

        $this->_model = $helper->getModel('Mage_Webapi_Model_Authorization_Loader_Rule', array(
            'ruleResource' => $this->_resourceModelMock,
        ));

        $this->_role = new Mage_Webapi_Model_Authorization_Role($this->_roleId);
        $this->_resource = new Magento_Acl_Resource($this->_resourceId);
        $this->_resourceDeny = new Magento_Acl_Resource($this->_resourceDenyId);
    }

    /**
     * Data provider for testPopulateAcl
     *
     * @return array
     */
    public function populateAclDataProvider()
    {
        return array(
            'with_rules' => array(
                'getRuleList' =>  array(
                    array(
                        'role_id' => $this->_roleId,
                        'resource_id' => $this->_resourceId
                    )
                ),
                'resource' => true,
                'resourceDeny' =>false

            ),
            'with_no_rules' => array(
                'getRuleList' =>  array(),
                'resource' => false,
                'resourceDeny' =>false
            )
        );
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Rule::populateAcl
     *
     * @param array $ruleList
     * @param bool $resource
     * @param bool $resourceDeny
     *
     * @dataProvider populateAclDataProvider()
     */
    public function testPopulateAcl($ruleList, $resource, $resourceDeny)
    {
        $acl = new Magento_Acl();
        $acl->addRole($this->_role);
        $acl->deny($this->_role);
        $acl->addResource($this->_resource);
        $acl->addResource($this->_resourceDeny);
        $this->_resourceModelMock->expects($this->once())->method('getRuleList')->will($this->returnValue($ruleList));
        $this->_model->populateAcl($acl);
        $this->assertEquals($resource, $acl->isAllowed($this->_roleId, $this->_resourceId));
        $this->assertEquals($resourceDeny, $acl->isAllowed($this->_roleId, $this->_resourceDenyId));
    }
}
