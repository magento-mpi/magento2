<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\File
     */
    protected $_asset;

    public function setUp()
    {
        $this->_asset = new \Magento\View\Asset\File(
            'filePath.ext',
            'fileSource.ext',
            'baseUrl'
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage An extension is expected in file path: fileNoExt
     */
    public function testConstructorException()
    {
        $this->_asset = new \Magento\View\Asset\File(
            'fileNoExt',
            'fileSource.ext',
            'baseUrl'
        );
    }

    public function testGetUrl()
    {
        $this->assertEquals('baseUrlfilePath.ext', $this->_asset->getUrl());
    }

    public function testGetContentType()
    {
        $this->assertEquals('ext', $this->_asset->getContentType());
    }

    public function testGetSourceFile()
    {
        $this->assertEquals('fileSource.ext', $this->_asset->getSourceFile());
    }

    public function testGetRelativePath()
    {
        $this->assertEquals('filePath.ext', $this->_asset->getRelativePath());
    }

    public function testGetFilePath()
    {
        $this->assertEquals('filePath.ext', $this->_asset->getFilePath());
    }
}
