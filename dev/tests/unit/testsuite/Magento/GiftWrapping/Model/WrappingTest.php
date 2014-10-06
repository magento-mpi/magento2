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

    /** @var string */
    protected $testImagePath;

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
        $this->testImagePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'magento_image.jpg';
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

    /**
     * @dataProvider invalidBinaryImageDataProvider
     * @param $fileName
     * @param $imageContent
     * @param $exceptionMessage
     */
    public function testAttachBinaryImageExceptions($fileName, $imageContent, $exceptionMessage)
    {
        $this->setExpectedException('Magento\Framework\Exception\InputException', $exceptionMessage);
        $this->wrapping->attachBinaryImage($fileName, $imageContent);
    }

    /**
     * @return array
     */
    public function invalidBinaryImageDataProvider()
    {
        return [
            ['image.php', 'image content', 'The image extension "php" not allowed'],
            ['{}?*:<>.jpg', 'image content', 'Provided image name contains forbidden characters'],
            ['image.jpg', 'image content', 'The image content must be valid data']
        ];
    }

    public function testAttachBinaryImageWriteFail()
    {
        $imageContent = file_get_contents($this->testImagePath);
        list($fileName, $imageContent, $absolutePath, $result) = ['image.jpg', $imageContent, 'absolutePath', false];
        $this->mediaDirectoryMock->expects($this->once())->method('getAbsolutePath')
            ->with(Wrapping::IMAGE_PATH . $fileName)->will($this->returnValue($absolutePath));
        $this->mediaDirectoryMock->expects($this->once())->method('writeFile')
            ->with(Wrapping::IMAGE_TMP_PATH . $absolutePath, $imageContent)->will($this->returnValue($result));
        $this->assertFalse($this->wrapping->attachBinaryImage($fileName, $imageContent));
    }

    public function testAttachBinaryImage()
    {
        $imageContent = file_get_contents($this->testImagePath);
        list($fileName, $imageContent, $absolutePath, $result) = ['image.jpg', $imageContent, 'absolutePath', true];
        $this->mediaDirectoryMock->expects($this->once())->method('getAbsolutePath')
            ->with(Wrapping::IMAGE_PATH . $fileName)->will($this->returnValue($absolutePath));
        $this->mediaDirectoryMock->expects($this->once())->method('writeFile')
            ->with(Wrapping::IMAGE_TMP_PATH . $absolutePath, $imageContent)->will($this->returnValue($result));

        $this->assertEquals($absolutePath, $this->wrapping->attachBinaryImage($fileName, $imageContent));
        $this->assertEquals($fileName, $this->wrapping->getData('tmp_image'));
        $this->assertEquals($fileName, $this->wrapping->getData('image'));
    }
}
