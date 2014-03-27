<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Css\PreProcessor;

class LessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Less\FileGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileGenerator;

    /**
     * @var \Magento\Css\PreProcessor\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    /**
     * @var \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    /**
     * @var \Magento\Css\PreProcessor\Less
     */
    private $object;

    protected function setUp()
    {
        $this->fileGenerator = $this->getMock('\Magento\Less\FileGenerator', array(), array(), '', false);
        $this->adapter = $this->getMockForAbstractClass('\Magento\Css\PreProcessor\AdapterInterface');
        $this->logger = $this->getMock('\Magento\Logger', array(), array(), '', false);
        $this->object = new \Magento\Css\PreProcessor\Less($this->fileGenerator, $this->adapter, $this->logger);
    }

    public function testProcess()
    {
        $expectedContent = 'updated content';
        $tmpFile = 'tmp/file.ext';
        $asset = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $this->fileGenerator->expects($this->once())
            ->method('generateLessFileTree')
            ->with('content', $asset)
            ->will($this->returnValue($tmpFile));
        $this->adapter->expects($this->once())
            ->method('process')
            ->with($tmpFile)
            ->will($this->returnValue($expectedContent));
        $actual = $this->object->process('content', 'less', $asset);
        $this->assertSame([$expectedContent, 'css'], $actual);
    }

    /**
     * @param string $exception
     *
     * @dataProvider processExceptionDataProvider
     */
    public function testProcessException($exception)
    {
        $asset = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $this->fileGenerator->expects($this->once())
            ->method('generateLessFileTree')
            ->with('content', $asset)
            ->will($this->throwException($exception));
        $this->logger->expects($this->once())
            ->method('logException')
            ->with($exception);
        $actual = $this->object->process('content', 'less', $asset);
        $this->assertSame(['content', 'less'], $actual);
    }

    /**
     * @return array
     */
    public function processExceptionDataProvider()
    {
        return [
            'filesystem exception' => [new \Magento\Filesystem\FilesystemException('Exception message')],
            'adapter exception'    => [new \Magento\Css\PreProcessor\Adapter\AdapterException('Exception message')],
        ];
    }
}
