<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class WrappingTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Model\Wrapping */
    protected $wrapping;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mediaDirectoryMock;

    protected function setUp()
    {
        $this->filesystemMock = $this->getMock('Magento\Framework\App\Filesystem', [], [], '', false);
        $this->mediaDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\WriteInterface')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $this->filesystemMock->expects($this->once())->method('getDirectoryWrite')
            ->with(\Magento\Framework\App\Filesystem::MEDIA_DIR)->will($this->returnValue($this->mediaDirectoryMock));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->wrapping = $this->objectManagerHelper->getObject(
            'Magento\GiftWrapping\Model\Wrapping',
            [
                'filesystem' => $this->filesystemMock,
            ]
        );
    }

    /**
     * @dataProvider emptyImageArgumentsDataProvider
     * @param string $fileName
     * @param string $imageContent
     */
    public function testAttachBinaryImageEmptyArguments($fileName, $imageContent)
    {
        $this->assertFalse($this->wrapping->attachBinaryImage($fileName, $imageContent));
    }

    /**
     * @return array
     */
    public function emptyImageArgumentsDataProvider()
    {
        return [
            ['', '1'],
            ['1', '']
        ];
    }

    public function testAttachBinaryImageWriteFail()
    {
        list($fileName, $imageContent, $absolutePath, $result) = ['filename', 'imageContent', 'absolutePath', false];
        $this->mediaDirectoryMock->expects($this->once())->method('getAbsolutePath')
            ->with(Wrapping::IMAGE_PATH . $fileName)->will($this->returnValue($absolutePath));
        $this->mediaDirectoryMock->expects($this->once())->method('writeFile')
            ->with(Wrapping::IMAGE_TMP_PATH . $absolutePath, $imageContent)->will($this->returnValue($result));
        $this->assertFalse($this->wrapping->attachBinaryImage($fileName, $imageContent));
    }

    public function testAttachBinaryImageSuccessl()
    {
        list($fileName, $imageContent, $absolutePath, $result) = ['filename', 'imageContent', 'absolutePath', true];
        $this->mediaDirectoryMock->expects($this->once())->method('getAbsolutePath')
            ->with(Wrapping::IMAGE_PATH . $fileName)->will($this->returnValue($absolutePath));
        $this->mediaDirectoryMock->expects($this->once())->method('writeFile')
            ->with(Wrapping::IMAGE_TMP_PATH . $absolutePath, $imageContent)->will($this->returnValue($result));

        $this->assertEquals($absolutePath, $this->wrapping->attachBinaryImage($fileName, $imageContent));
        $this->assertEquals($fileName, $this->wrapping->getData('tmp_image'));
        $this->assertEquals($fileName, $this->wrapping->getData('image'));
    }
}
