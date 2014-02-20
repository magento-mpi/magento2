<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\File;

class MimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\File\Mime
     */
    private $object;

    protected function setUp()
    {
        $this->object = new \Magento\File\Mime;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File 'nonexistent.file' doesn't exist
     */
    public function testGetMimeTypeNonexistentFileException()
    {
        $file = 'nonexistent.file';
        $this->object->getMimeType($file);
    }

    /**
     * @param string $file
     * @param string $expectedType
     *
     * @dataProvider getMimeTypeDataProvider
     */
    public function testGetMimeType($file, $expectedType)
    {
        $actualType = $this->object->getMimeType($file);
        $this->assertSame($expectedType, $actualType);
    }

    /**
     * @return array
     */
    public function getMimeTypeDataProvider()
    {
        return array(
            'javascript' => array(__DIR__ . '/_files/javascript.js', 'application/javascript'),
            'weird extension' => array(__DIR__ . '/_files/file.weird', 'application/octet-stream'),
        );
    }
}
