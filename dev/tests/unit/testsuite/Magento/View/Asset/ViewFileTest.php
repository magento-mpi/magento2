<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class ViewFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\ViewFile
     */
    protected $_object;

    /**
     * @var \Magento\View\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewUrl;

    /**
     * @var \Magento\View\FileResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileResolver;

    protected function setUp()
    {
        $this->_viewUrl = $this->getMock('\Magento\View\Url', array(), array(), '', false);
        $this->_fileResolver = $this->getMock('\Magento\View\FileResolver', array(), array(), '', false);
        $this->_object = new ViewFile($this->_viewUrl, $this->_fileResolver, 'test/script.js', 'js');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Parameter 'file' must not be empty
     */
    public function testConstructorException()
    {
        new ViewFile($this->_viewUrl, $this->_fileResolver, '', 'unknown');
    }

    public function testGetUrl()
    {
        $url = 'http://127.0.0.1/magento/test/script.js';
        $this->_viewUrl
            ->expects($this->once())
            ->method('getViewFileUrl')
            ->with('test/script.js')
            ->will($this->returnValue($url))
        ;
        $this->assertEquals($url, $this->_object->getUrl());
    }

    public function testGetContentType()
    {
        $this->assertEquals('js', $this->_object->getContentType());
    }

    public function testGetSourceFile()
    {
        $sourcePath = '/source_dir/test/script.js';
        $this->_fileResolver
            ->expects($this->once())
            ->method('getViewFilePublicPath')
            ->with('test/script.js')
            ->will($this->returnValue($sourcePath))
        ;
        $this->assertEquals($sourcePath, $this->_object->getSourceFile());
    }
}
