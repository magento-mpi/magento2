<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Adapter/Factory.php';

class Magento_Test_Tools_Migration_Acl_Db_Adapter_FactoryTest extends PHPUnit_Framework_TestCase
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
            array('Magento_Db_Adapter_Pdo_Mysql'),
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
        $adapterMock = $this->getMock('Magento_Db_Adapter_Pdo_Mysql', array(), array(), '', false);

        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->any())
            ->method('create')
            ->with($this->equalTo('Magento_Db_Adapter_Pdo_Mysql'))
            ->will($this->returnValue($adapterMock));

        $factory = new Magento_Tools_Migration_Acl_Db_Adapter_Factory($objectManager);
        $adapter = $factory->getAdapter($this->_config, $adapterType);

        $this->assertInstanceOf('Zend_Db_Adapter_Abstract', $adapter);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetAdapterWithInvalidType()
    {
        $adapterType = 'Magento_Object';
        $adapterMock = $this->getMock($adapterType, array(), array(), '', false);

        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo($adapterType), $this->equalTo(array('config' => $this->_config)))
            ->will($this->returnValue($adapterMock));

        $factory = new Magento_Tools_Migration_Acl_Db_Adapter_Factory($objectManager);
        $factory->getAdapter($this->_config, $adapterType);
    }
}
