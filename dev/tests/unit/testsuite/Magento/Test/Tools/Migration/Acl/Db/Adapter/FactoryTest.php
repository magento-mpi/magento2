<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Adapter/Factory.php';

class Magento_Test_Tools_Migration_Acl_Db_Adapter_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Migration\Acl\Db\Adapter\Factory
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_config;

    protected function setUp()
    {
        $this->_model = new \Magento\Tools\Migration\Acl\Db\Adapter\Factory();
        $this->_config = array(
            'dbname' => 'some_db_name',
            'password' => '',
            'username' => '',

        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @return array
     */
    public function getAdapterDataProvider()
    {
        return array(
            array('Magento\DB\Adapter\Pdo\Mysql'),
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
        $this->assertInstanceOf('Zend_Db_Adapter_Abstract',
            $this->_model->getAdapter($this->_config, $adapterType)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetAdapterWithInvalidType()
    {
        $this->_model->getAdapter($this->_config, 'Magento\Object');
    }
}
