<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Config
     */
    protected $_config;

    /**
     * @var \Magento\App\Config
     */
    protected $_configWithOverriding;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loaderMock;

    protected function setUp()
    {
        $this->_loaderMock = $this->getMock('Magento\App\Config\Loader', array(), array(), '', false);
        $paramsFromConfig = array(
            'connection' => array(
                'default' => array('connection_name'),
                'default_overriding' => array('connection_name')
            ),
            'resource' => array(
                'name' => array('default_setup'),
                'name_overriding' => array('default_setup')
            ),
            'cache' => array(
                'type' => array('cache'),
                'type_overriding' => array('cache')
            )
        );
        $overridingParams = array(
            'connection' => array(
                'default_overriding' => array('connection_name_overriding'),
                'default_merging' => array('connection_name_merging')
            ),
            'resource' => array(
                'name_overriding' => array('default_setup_overriding'),
                'name_merging' => array('default_setup_merging')
            ),
            'cache' => array(
                'type_overriding' => array('cache_overriding'),
                'type_merging' => array('cache_merging')
            )
        );
        $this->_loaderMock->expects($this->any())->method('load')->will($this->returnValue($paramsFromConfig));
        $this->_config = new \Magento\App\Config(
            array(),
            $this->_loaderMock
        );
        $this->_configWithOverriding = new \Magento\App\Config(
            $overridingParams,
            $this->_loaderMock
        );
    }

    /**
     * @param string $connectionName
     * @param array|null $connectionDetail
     * @dataProvider getConnectionDataProvider
     */
    public function testGetConnection($connectionDetail, $connectionName)
    {
        $this->assertEquals($connectionDetail, $this->_config->getConnection($connectionName));
    }

    public function getConnectionDataProvider()
    {
        return array(
            'connection_name_exist' => array(array('connection_name'), 'default'),
            'connection_name_not_exist' => array(null, 'new_default')
        );
    }

    public function testGetConnections()
    {
        $this->assertEquals(
            array(
                'default' => array('connection_name'),
                'default_overriding' => array('connection_name')
            ),
            $this->_config->getConnections()
        );
        $this->assertEquals(
            array(
                'default' => array('connection_name'),
                'default_overriding' => array('connection_name_overriding'),
                'default_merging' => array('connection_name_merging')
            ),
            $this->_configWithOverriding->getConnections()
        );
    }

    public function testGetResources()
    {
        $this->assertEquals(
            array(
                'name' => array('default_setup'),
                'name_overriding' => array('default_setup')
            ),
            $this->_config->getResources()
        );
        $this->assertEquals(
            array(
                'name' => array('default_setup'),
                'name_overriding' => array('default_setup_overriding'),
                'name_merging' => array('default_setup_merging')
            ),
            $this->_configWithOverriding->getResources()
        );
    }

    public function testGetCacheSettings()
    {
        $this->assertEquals(
            array(
                'type' => array('cache'),
                'type_overriding' => array('cache')
            ),
            $this->_config->getCacheSettings()
        );
        $this->assertEquals(
            array(
                'type' => array('cache'),
                'type_overriding' => array('cache_overriding'),
                'type_merging' => array('cache_merging'),
            ),
            $this->_configWithOverriding->getCacheSettings()
        );
    }
}
