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
 * Test class for Mage_Webapi_Model_Authorization_Config
 */
class Mage_Webapi_Model_Authorization_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Authorization_Config
     */
    protected $_model;

    /**
     * @var Magento_Acl_Config_Reader
     */
    protected $_configReader;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $this->_config = $this->getMock('Mage_Core_Model_Config',
            array('getModelInstance', 'getModuleConfigurationFiles'), array(), '', false);
        $this->_configReader = $this->getMock('Magento_Acl_Config_Reader',
            array('getAclResources'), array(), '', false);
        $this->_model = new Mage_Webapi_Model_Authorization_Config(array(
            'config' => $this->_config
        ));
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Config::getAclResources()
     */
    public function testGetAclResources()
    {
        $aclResources = new DOMDocument();
        $aclResources->load(__DIR__ . DIRECTORY_SEPARATOR .  '..'
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'acl.xml');
        $this->_configReader->expects($this->once())
            ->method('getAclResources')
            ->will($this->returnValue($aclResources));
        $this->_config->expects($this->once())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue(array()));
        $this->_config->expects($this->once())
            ->method('getModelInstance')
            ->will($this->returnValue($this->_configReader));

        $expectedResources = array('customer', 'customer/create', 'customer/update');
        $resources = $this->_model->getAclResources();

        $this->assertInstanceOf('DOMNodeList', $resources);
        $actualResources = $this->getResources($resources);
        sort($expectedResources);
        sort($actualResources);
        $this->assertEquals($expectedResources, $actualResources);
    }

    /**
     *
     *
     * @param $resources
     * @return array
     */
    public function getResources($resources)
    {
        $resourceArray = array();
        /** @var $resource DOMElement */
        foreach ($resources as $resource) {
            if (!($resource instanceof DOMElement)) {
                continue;
            }
            $resourceArray = array_merge($resourceArray, array($resource->getAttribute('id')));
            if ($resource->hasChildNodes()) {
                $resourceArray = array_merge($resourceArray, $this->getResources($resource->childNodes));
            }
        }
        return $resourceArray;
    }
}
