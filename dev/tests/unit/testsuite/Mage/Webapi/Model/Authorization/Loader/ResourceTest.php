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
     * Set up before test
     */
    protected function setUp()
    {
        /** @var $objectFactory Mage_Core_Model_Config */
        $objectFactory = $this->getMock('Mage_Core_Model_Config',
            array('getModelInstance'), array(), '', false);
        $getModel = function()
        {
            return new Magento_Acl_Resource(func_get_arg(1));
        };
        $objectFactory->expects($this->any())
            ->method('getModelInstance')
            ->will($this->returnCallback($getModel));
        /** @var $config Mage_Webapi_Model_Authorization_Config */
        $config = $this->getMock('Mage_Webapi_Model_Authorization_Config',
            array('getAclResources', 'getAclVirtualResources'), array(), '', false);
        $config->expects($this->once())
            ->method('getAclResources')
            ->will($this->returnValue($this->getResourceXPath()->query('/config/acl/resources/*')));
        $config->expects($this->once())
            ->method('getAclVirtualResources')
            ->will($this->returnValue($this->getResourceXPath()->query('/config/mapping/*')));
        $this->_model = new Mage_Webapi_Model_Authorization_Loader_Resource(array(
            'objectFactory' => $objectFactory,
            'config' => $config,
        ));
    }


    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Resource::populateAcl
     *
     * @param bool $parent
     * @param bool $resource
     * @param bool $allowedParent
     * @param bool $allowedResource
     * @param bool $allowedVirtual
     *
     * @dataProvider populateAclDataProvider
     */
    public function testPopulateAcl($parent, $resource, $allowedParent, $allowedResource, $allowedVirtual)
    {
        $parentName = 'customer';
        $resourceName = 'customer/get';
        $virtualName = 'customer/list';
        $acl = new Magento_Acl();
        $this->_model->populateAcl($acl);
        $acl->allow(null, 'customer');
        if ($parent === true) {
            $acl->allow(null, $parentName);
        } elseif ($parent === false) {
            $acl->deny(null, $parentName);
        }
        if ($resource === true) {
            $acl->allow(null, $resourceName);
        } elseif ($resource === false) {
            $acl->deny(null, $resourceName);
        }
        $this->assertEquals($allowedParent, $acl->isAllowed(null, $parentName));
        $this->assertEquals($allowedResource, $acl->isAllowed(null, $resourceName));
        $this->assertEquals($allowedVirtual, $acl->isAllowed(null, $virtualName));
    }

    /**
     * Data provider for testPopulateAcl
     *
     * @return array
     */
    public function populateAclDataProvider()
    {
        return array(
            array(
                'parent' => true,
                'resource' => false,
                'allowedParent' => true,
                'allowedResource' => false,
                'allowedVirtual' => false
            ),
            array(
                'parent' => true,
                'resource' => null,
                'allowedParent' => true,
                'allowedResource' => false,
                'allowedVirtual' => false
            ),
            array(
                'parent' => false,
                'resource' => true,
                'allowedParent' => false,
                'allowedResource' => true,
                'allowedVirtual' => true
            ),
        );
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
