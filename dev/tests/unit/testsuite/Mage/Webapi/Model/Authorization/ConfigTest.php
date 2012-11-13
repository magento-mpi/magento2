<?php
/**
 * Test class for Mage_Webapi_Model_Authorization_Config
 *
 * @copyright {}
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
     * @var Mage_Webapi_Model_Authorization_Config_ReaderFactory
     */
    protected $_readerFactory;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_config = $this->getMock('Mage_Core_Model_Config',
            array('getModuleConfigurationFiles'), array(), '', false);

        $this->_readerFactory = $this->getMock('Mage_Webapi_Model_Authorization_Config_ReaderFactory',
            array('createReader'), array(), '', false);

        $this->_configReader = $this->getMock('Magento_Acl_Config_Reader',
            array('getAclResources'), array(), '', false);

        $this->_model = $helper->getModel('Mage_Webapi_Model_Authorization_Config', array(
            'config' => $this->_config,
            'readerFactory' => $this->_readerFactory
        ));

        $this->_config->expects($this->once())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue(array()));
        $this->_readerFactory->expects($this->once())
            ->method('createReader')
            ->will($this->returnValue($this->_configReader));
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

        $expectedResources = array(
            'Mage_Webapi',
            'customer',
            'customer/create',
            'customer/delete',
            'customer/get',
            'customer/update'
        );
        $resources = $this->_model->getAclResources();

        $this->assertInstanceOf('DOMNodeList', $resources);
        $actualResources = $this->getResources($resources);
        sort($expectedResources);
        sort($actualResources);
        $this->assertEquals($expectedResources, $actualResources);
    }

    /**
     * Get resources array recursively
     *
     * @param DOMNodeList $resources
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

    /**
     * test for Mage_Webapi_Model_Authorization_ConfigTest::getAclVirtualResources
     */
    public function testGetAclVirtualResources()
    {
        $aclResources = new DOMDocument();
        $aclResources->load(__DIR__ . DIRECTORY_SEPARATOR .  '..'
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'acl.xml');
        $this->_configReader->expects($this->once())
            ->method('getAclResources')
            ->will($this->returnValue($aclResources));

        $expectedResources = array(array(
            'id' => 'customer/list',
            'parent' => 'customer/get'
        ));
        $resources = $this->_model->getAclVirtualResources();

        $this->assertInstanceOf('DOMNodeList', $resources);
        $actualResources = array();
        foreach ($resources as $resourceConfig) {
            if (!($resourceConfig instanceof DOMElement)) {
                continue;
            }
            $actualResources[] = array(
                'id' => $resourceConfig->getAttribute('id'),
                'parent' => $resourceConfig->getAttribute('parent')
            );
        }
        sort($expectedResources);
        sort($actualResources);
        $this->assertEquals($expectedResources, $actualResources);
    }
}
