<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

namespace Magento\Integration\Model\Config;

use Magento\Integration\Model\Config\Reader as ConfigReader;

/**
 * Integration config reader test.
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_fileResolverMock;

    /** @var ConfigReader */
    protected $_configReader;

    protected function setUp()
    {
        parent::setUp();
        $this->_fileResolverMock = $this->getMock('Magento\Config\FileResolverInterface');
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_configReader = $objectManager->create(
            'Magento\Integration\Model\Config\Reader',
            array('fileResolver' => $this->_fileResolverMock)
        );
    }

    public function testRead()
    {
        $configFiles = array(
            realpath(__DIR__ . '/_files/integrationA.xml'),
            realpath(__DIR__ . '/_files/integrationB.xml')
        );
        $this->_fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($configFiles));

        $expectedResult = require __DIR__ . '/_files/integration.php';
        $this->assertEquals(
            $expectedResult,
            $this->_configReader->read(),
            'Error happened during config reading.'
        );
    }
}
