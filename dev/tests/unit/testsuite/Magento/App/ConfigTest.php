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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loaderMock;

    protected function setUp()
    {
        $this->_loaderMock = $this->getMock('Magento\App\Config\Loader', array(), array(), '', false);
        $params = array(
          'connection' => array('default' => array('connection_name')),
          'resource' => array('name' => array('default_setup'))
        );
        $this->_loaderMock->expects($this->any())->method('load')->will($this->returnValue($params));
        $this->_config = new \Magento\App\Config(
            array(),
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
        $this->assertEquals(array('default' => array('connection_name')), $this->_config->getConnections());
    }

    public function testGetResources()
    {
        $this->assertEquals(array('name' => array('default_setup')), $this->_config->getResources());
    }
}
