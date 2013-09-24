<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Attribute_Config_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Attribute_Config_Data
     */
    protected $_model;

    /**
     * @var Magento_Catalog_Model_Attribute_Config_Reader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configReader;

    /**
     * @var Magento_Config_ScopeInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScope;

    /**
     * @var Magento_Config_CacheInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configCache;

    protected function setUp()
    {
        $this->_configReader = $this->getMock(
            'Magento_Catalog_Model_Attribute_Config_Reader', array('read'), array(), '', false
        );
        $this->_configCache = $this->getMock('Magento_Config_CacheInterface');
        $this->_model = new Magento_Catalog_Model_Attribute_Config_Data(
            $this->_configReader, $this->_configCache, 'fixture_cache_id', 'fixture_scope'
        );
    }

    public function testGetData()
    {
        $fixtureConfigData = require __DIR__ . '/_files/attributes_config_merged.php';
        $this->_configCache
            ->expects($this->once())
            ->method('load')
            ->with('fixture_scope::fixture_cache_id')
            ->will($this->returnValue(false))
        ;
        $this->_configCache
            ->expects($this->once())
            ->method('save')
            ->with(serialize($fixtureConfigData), 'fixture_scope::fixture_cache_id')
        ;
        $this->_configReader
            ->expects($this->once())
            ->method('read')
            ->with('fixture_scope')
            ->will($this->returnValue($fixtureConfigData))
        ;
        $this->assertEquals($fixtureConfigData, $this->_model->getData('group_one'));
        // Makes sure the value is calculated only once
        $this->assertEquals($fixtureConfigData, $this->_model->getData('group_one'));
    }
}
