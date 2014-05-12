<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Tools\Migration\Acl\Db\Logger;


require_once realpath(
    __DIR__ . '/../../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/Acl/Db/AbstractLogger.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/Acl/Db/Logger/Factory.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/Acl/Db/Logger/Console.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/Acl/Db/Logger/File.php';
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Migration\Acl\Db\Logger\Factory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Tools\Migration\Acl\Db\Logger\Factory();
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
        return array(array('console', null), array('file', realpath(__DIR__ . '/../../../../../') . '/tmp'));
    }

    /**
     * @param string $loggerType
     * @param string $file
     * @dataProvider getLoggerDataProvider
     */
    public function testGetLogger($loggerType, $file)
    {
        $this->assertInstanceOf(
            'Magento\Tools\Migration\Acl\Db\AbstractLogger',
            $this->_model->getLogger($loggerType, $file)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetLoggerWithInvalidType()
    {
        $this->_model->getLogger('invalid type');
    }
}
