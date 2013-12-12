<?php
/**
 * \Magento\Core\Model\DataService\Config\Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Model\DataService\Config\Reader */
    private $_configReader;

    /** @var \PHPUnit_Framework_MockObject_MockObject  */
    private $_modulesReaderMock;

    /**
     * Prepare object manager with mocks of objects required by config reader.
     */
    protected function setUp()
    {
        $path = array(__DIR__, '..', '_files', 'service_calls.xml');
        $path = realpath(implode('/', $path));
        $this->_modulesReaderMock = $this->getMockBuilder('Magento\Module\Dir\Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $directoryMock = $this->getMockBuilder('\Magento\Filesystem\Directory\Read')
            ->disableOriginalConstructor()
            ->getMock();

        $directoryMock->expects($this->any())
            ->method('readFile')
            ->will($this->returnValue(file_get_contents($path)));

        $this->_configReader = new \Magento\Core\Model\DataService\Config\Reader(
            new \Magento\Config\FileIterator($directoryMock, array($path)),
            $this->_modulesReaderMock
        );
    }

    /**
     * Verify correct schema file is returned.
     */
    public function testGetSchemaFile()
    {
        $etcDir = 'app/code/Magento/Core/etc';
        $expectedPath = $etcDir . '/service_calls.xsd';
        $this->_modulesReaderMock->expects($this->any())->method('getModuleDir')
            ->with('etc', 'Magento_Core')
            ->will($this->returnValue($etcDir));
        $result = $this->_configReader->getSchemaFile();
        $this->assertNotNull($result);
        $this->assertEquals($expectedPath, $result, 'returned schema file is wrong');
    }
}
