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
    . '/tools/Magento/Tools/Migration/Acl/Db/LoggerAbstract.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Logger/Factory.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Logger/Console.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Logger/File.php';



class Magento_Test_Tools_Migration_Acl_Db_Logger_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Migration\Acl\Db\Logger\Factory
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new \Magento\Tools\Migration\Acl\Db\Logger\Factory();
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @return array
     */
    public function getLoggerDataProvider()
    {
        return array(
            array('console', null),
            array('file', realpath(dirname(__FILE__) . '/../../../../../') . '/tmp') ,
        );
    }

    /**
     * @param string $loggerType
     * @param string $file
     * @dataProvider getLoggerDataProvider
     */
    public function testGetLogger($loggerType, $file)
    {
        $this->assertInstanceOf(
            'Magento\Tools\Migration\Acl\Db\LoggerAbstract', $this->_model->getLogger($loggerType, $file));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetLoggerWithInvalidType()
    {
        $this->_model->getLogger('invalid type');
    }
}

