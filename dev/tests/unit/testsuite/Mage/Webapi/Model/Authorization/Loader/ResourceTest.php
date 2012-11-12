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
 * Test class for Mage_Webapi_Model_Authorization_Loader_Resource
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
     * @var Magento_Acl_Resource
     */
    protected $_resource;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_resource = new Magento_Acl_Resource('test resource');

        /** @var $resourceFactory Magento_Acl_ResourceFactory */
        $resourceFactory = $this->getMock('Magento_Acl_ResourceFactory',
            array('createResource'), array(), '', false);
        $resourceFactory->expects($this->any())
            ->method('createResource')
            ->will($this->returnValue($this->_resource));

        /** @var $config Mage_Webapi_Model_Authorization_Config */
        $config = $this->getMock('Mage_Webapi_Model_Authorization_Config',
            array('getAclResources', 'getAclVirtualResources'), array(), '', false);
        $config->expects($this->once())
            ->method('getAclResources')
            ->will($this->returnValue($this->getResourceXPath()->query('/config/acl/resources/*')));
        $config->expects($this->once())
            ->method('getAclVirtualResources')
            ->will($this->returnValue($this->getResourceXPath()->query('/config/mapping/*')));

        $this->_model = $helper->getModel('Mage_Webapi_Model_Authorization_Loader_Resource', array(
            'resourceFactory' => $resourceFactory,
            'config' => $config,
        ));

        $this->_acl = $this->getMock('Magento_Acl', array('has', 'addResource', 'deny', 'getResources'), array(), '',
            false);
    }


    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Resource::populateAcl
     */
    public function testPopulateAcl()
    {
        $resources = array('resource1', 'resource2');
        $this->_acl->expects($this->once())
            ->method('getResources')
            ->will($this->returnValue($resources));
        $this->_acl->expects($this->exactly(2))
            ->method('deny')
            ->with(null, $this->logicalOr(
                $this->equalTo('resource1'),
                $this->equalTo('resource2')
            ))
            ->will($this->returnValue(null));
        $this->_acl->expects($this->exactly(2))
            ->method('has')
            ->with($this->logicalOr(
                $this->equalTo('customer/list'),
                $this->equalTo('customer/get')
            ))
            ->will($this->returnCallback(array($this, 'aclHasResource')));
        $this->_acl->expects($this->exactly(7))
            ->method('addResource')
            ->will($this->returnValue(null));

        $this->_model->populateAcl($this->_acl);
    }

    /**
     * @param string $resourceId
     * @return bool
     */
    public function aclHasResource($resourceId)
    {
        return $resourceId == 'customer/get' ? true : false;
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
