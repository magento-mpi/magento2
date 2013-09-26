<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Attribute\Config\Data
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Attribute\Config\Reader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configReader;

    /**
     * @var \Magento\Config\ScopeInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScope;

    /**
     * @var \Magento\Config\CacheInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configCache;

    protected function setUp()
    {
        $this->_configReader = $this->getMock(
            'Magento\Catalog\Model\Attribute\Config\Reader', array('read'), array(), '', false
        );
        $this->_configCache = $this->getMock('Magento\Config\CacheInterface');
        $this->_model = new \Magento\Catalog\Model\Attribute\Config\Data(
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
