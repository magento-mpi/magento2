<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class ArgumentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Arguments
     */
    protected $_arguments;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loaderMock;

    protected function setUp()
    {
        $this->_loaderMock = $this->getMock('Magento\App\Arguments\Loader', array(), array(), '', false);
        $params = array(
          'connection' => array('default' => array('connection_name')),
          'resource' => array('name' => array('default_setup'))
        );
        $this->_loaderMock->expects($this->any())->method('load')->will($this->returnValue($params));
        $this->_arguments = new \Magento\App\Arguments(
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
        $this->assertEquals($connectionDetail, $this->_arguments->getConnection($connectionName));
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
        $this->assertEquals(array('default' => array('connection_name')), $this->_arguments->getConnections());
    }

    public function testGetResources()
    {
        $this->assertEquals(array('name' => array('default_setup')), $this->_arguments->getResources());
    }
}
