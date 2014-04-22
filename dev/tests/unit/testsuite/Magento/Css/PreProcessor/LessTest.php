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
     * @var \Magento\Framework\View\Asset\PreProcessor\Chain
     */
    private $chain;

    /**
     * @var \Magento\Css\PreProcessor\Less
     */
    private $object;

    protected function setUp()
    {
        $this->fileGenerator = $this->getMock('\Magento\Less\FileGenerator', array(), array(), '', false);
        $this->adapter = $this->getMockForAbstractClass('\Magento\Css\PreProcessor\AdapterInterface');
        $this->logger = $this->getMock('\Magento\Logger', array(), array(), '', false);
        $asset = $this->getMockForAbstractClass('\Magento\View\Asset\LocalInterface');
        $asset->expects($this->once())->method('getContentType')->will($this->returnValue('origType'));
        $this->chain = new \Magento\Framework\View\Asset\PreProcessor\Chain($asset, 'original content', 'origType');
        $this->object = new \Magento\Css\PreProcessor\Less($this->fileGenerator, $this->adapter, $this->logger);
    }

    public function testProcess()
    {
        $expectedContent = 'updated content';
        $tmpFile = 'tmp/file.ext';
        $this->fileGenerator->expects($this->once())
            ->method('generateLessFileTree')
            ->with($this->chain)
            ->will($this->returnValue($tmpFile));
        $this->adapter->expects($this->once())
            ->method('process')
            ->with($tmpFile)
            ->will($this->returnValue($expectedContent));
        $this->object->process($this->chain);
        $this->assertEquals($expectedContent, $this->chain->getContent());
        $this->assertEquals('css', $this->chain->getContentType());
    }

    /**
     * @param string $exception
     *
     * @dataProvider processExceptionDataProvider
     */
    public function testProcessException($exception)
    {
        $this->fileGenerator->expects($this->once())
            ->method('generateLessFileTree')
            ->with($this->chain)
            ->will($this->throwException($exception));
        $this->logger->expects($this->once())
            ->method('logException')
            ->with($exception);
        $this->object->process($this->chain);
        $this->assertEquals('original content', $this->chain->getContent());
        $this->assertEquals('origType', $this->chain->getContentType());
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
