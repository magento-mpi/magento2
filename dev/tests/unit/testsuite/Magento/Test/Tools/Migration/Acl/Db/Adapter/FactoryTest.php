<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Migration\Acl\Db\Adapter;

require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Adapter/Factory.php';

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $_config;

    protected function setUp()
    {
        $this->_config = array(
            'dbname' => 'some_db_name',
            'password' => '',
            'username' => '',
        );
    }

    /**
     * @return array
     */
    public function getAdapterDataProvider()
    {
        return array(
            array('Magento\Db\Adapter\Pdo\Mysql'),
            array(''),
            array(null),
        );
    }

    /**
     * @param $adapterType
     * @dataProvider getAdapterDataProvider
     */
    public function testGetAdapter($adapterType)
    {
        $adapterMock = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false);

        $objectManager = $this->getMock('Magento\ObjectManager');
        $objectManager->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Magento\Db\Adapter\Pdo\Mysql'))
            ->will($this->returnValue($adapterMock));

        $factory = new \Magento\Tools\Migration\Acl\Db\Adapter\Factory($objectManager);
        $adapter = $factory->getAdapter($this->_config, $adapterType);

        $this->assertInstanceOf('Zend_Db_Adapter_Abstract', $adapter);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetAdapterWithInvalidType()
    {
        $adapterType = 'Magento\Object';
        $adapterMock = $this->getMock($adapterType, array(), array(), '', false);

        $objectManager = $this->getMock('Magento\ObjectManager');
        $objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo($adapterType), $this->equalTo(array('config' => $this->_config)))
            ->will($this->returnValue($adapterMock));

        $factory = new \Magento\Tools\Migration\Acl\Db\Adapter\Factory($objectManager);
        $factory->getAdapter($this->_config, $adapterType);
    }
}
