<?php
/**
 * Test class for Mage_Webapi_Model_Authorization_Loader_Resource
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Authorization_Loader_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Authorization_Loader_Resource
     */
    protected $_model;

    /**
     * @var Magento_Acl
     */
    protected $_acl;

    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_helper;

    /**
     * @var Mage_Webapi_Model_Authorization_Config
     */
    protected $_config;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $this->_helper = new Magento_Test_Helper_ObjectManager($this);

        $resource = new Magento_Acl_Resource('test resource');

        /** @var $resourceFactory Magento_Acl_ResourceFactory */
        $resourceFactory = $this->getMock('Magento_Acl_ResourceFactory',
            array('createResource'), array(), '', false);
        $resourceFactory->expects($this->any())
            ->method('createResource')
            ->will($this->returnValue($resource));

        $this->_config = $this->getMock('Mage_Webapi_Model_Authorization_Config',
            array('getAclResources', 'getAclVirtualResources'), array(), '', false);
        $this->_config->expects($this->once())
            ->method('getAclResources')
            ->will($this->returnValue($this->getResourceXPath()->query('/config/acl/resources/*')));

        $this->_model = $this->_helper->getModel('Mage_Webapi_Model_Authorization_Loader_Resource', array(
            'resourceFactory' => $resourceFactory,
            'config' => $this->_config,
        ));

        $this->_acl = $this->getMock('Magento_Acl', array('has', 'addResource', 'deny', 'getResources'), array(), '',
            false);
    }


    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Resource::populateAcl
     */
    public function testPopulateAcl()
    {
        $resources = array('customer/get', 'customer/list');

        $this->_config->expects($this->once())
            ->method('getAclVirtualResources')
            ->will($this->returnValue($this->getResourceXPath()->query('/config/mapping/*')));

        $this->_acl->expects($this->once())
            ->method('getResources')
            ->will($this->returnValue($resources));
        $this->_acl->expects($this->exactly(2))
            ->method('deny')
            ->with(null, call_user_func_array(array($this, 'logicalOr'), $resources));
        $this->_acl->expects($this->exactly(2))
            ->method('has')
            ->with(call_user_func_array(array($this, 'logicalOr'), $resources))
            ->will($this->returnValueMap(array(
                array($resources[0], true),
                array($resources[1], false)
            )));
        $this->_acl->expects($this->exactly(7))
            ->method('addResource');

        $this->_model->populateAcl($this->_acl);
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Resource::populateAcl with invalid Virtual resources DOM
     */
    public function testPopulateAclWithInvalidDOM()
    {
        $resources = array('customer/get', 'customer/list');

        $this->_config->expects($this->once())
            ->method('getAclVirtualResources')
            ->will($this->returnValue(array(3)));

        $this->_acl->expects($this->once())
            ->method('getResources')
            ->will($this->returnValue($resources));
        $this->_acl->expects($this->exactly(2))
            ->method('deny')
            ->with(null, call_user_func_array(array($this, 'logicalOr'), $resources));

        $this->_model->populateAcl($this->_acl);
    }

    /**
     * Get Resources DOMXPath from fixture
     *
     * @return DOMXPath
     */
    public function getResourceXPath()
    {
        $aclResources = new DOMDocument();
        $aclResources->load(__DIR__ . DIRECTORY_SEPARATOR .  '..' . DIRECTORY_SEPARATOR .  '..'
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'acl.xml');
        return new DOMXPath($aclResources);
    }
}
