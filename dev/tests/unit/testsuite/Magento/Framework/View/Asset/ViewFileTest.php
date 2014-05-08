<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset;

class ViewFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Asset\ViewFile
     */
    protected $_object;

    /**
     * @var \Magento\Framework\View\Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewUrl;

    protected function setUp()
    {
        $this->_viewUrl = $this->getMock('Magento\Framework\View\Url', array(), array(), '', false);
        $this->_object = new \Magento\Framework\View\Asset\ViewFile($this->_viewUrl, 'test/script.js', 'js');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Parameter 'file' must not be empty
     */
    public function testConstructorException()
    {
        new \Magento\Framework\View\Asset\ViewFile($this->_viewUrl, '', 'unknown');
    }

    public function testGetUrl()
    {
        $url = 'http://127.0.0.1/magento/test/script.js';
        $this->_viewUrl->expects(
            $this->once()
        )->method(
            'getViewFileUrl'
        )->with(
            'test/script.js'
        )->will(
            $this->returnValue($url)
        );
        $this->assertEquals($url, $this->_object->getUrl());
    }

    public function testGetContentType()
    {
        $this->assertEquals('js', $this->_object->getContentType());
    }

    public function testGetSourceFile()
    {
        $sourcePath = '/source_dir/test/script.js';
        $this->_viewUrl->expects(
            $this->once()
        )->method(
            'getViewFilePublicPath'
        )->with(
            'test/script.js'
        )->will(
            $this->returnValue($sourcePath)
        );
        $this->assertEquals($sourcePath, $this->_object->getSourceFile());
    }
}
