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
     * @var Magento_Tools_Migration_Acl_Db_Logger_Factory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_Tools_Migration_Acl_Db_Logger_Factory();
    }

    protected function tearDown()
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
            'Magento_Tools_Migration_Acl_Db_LoggerAbstract', $this->_model->getLogger($loggerType, $file));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetLoggerWithInvalidType()
    {
        $this->_model->getLogger('invalid type');
    }
}

