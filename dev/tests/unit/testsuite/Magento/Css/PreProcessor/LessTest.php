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
        $asset = $this->getMockForAbstractClass('\Magento\Framework\View\Asset\LocalInterface');
        $asset->expects($this->once())->method('getContentType')->will($this->returnValue('origType'));
        $this->chain = new \Magento\Framework\View\Asset\PreProcessor\Chain($asset, 'original content', 'origType');
        $this->object = new \Magento\Css\PreProcessor\Less($this->fileGenerator, $this->adapter);
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
}
