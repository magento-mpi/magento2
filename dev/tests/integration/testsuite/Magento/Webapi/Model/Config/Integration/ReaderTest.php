<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

namespace Magento\Webapi\Model\Config\Integration;

use Magento\Webapi\Model\Config\Integration\Reader as ConfigReader;

/**
 * Integration API config reader test.
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
            'Magento\Webapi\Model\Config\Integration\Reader',
            array('fileResolver' => $this->_fileResolverMock)
        );
    }

    public function testRead()
    {
        $configFiles = array(
            file_get_contents(realpath(__DIR__ . '/_files/apiA.xml')),
            file_get_contents(realpath(__DIR__ . '/_files/apiB.xml'))
        );
        $this->_fileResolverMock->expects($this->any())->method('get')->will($this->returnValue($configFiles));

        $expectedResult = require __DIR__ . '/_files/api.php';
        $this->assertEquals(
            $expectedResult,
            $this->_configReader->read(),
            'Error happened during config reading.'
        );
    }
}
