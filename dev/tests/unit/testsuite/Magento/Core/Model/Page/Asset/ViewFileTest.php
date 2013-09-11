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

class Magento_Core_Model_Page_Asset_ViewFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Page\Asset\ViewFile
     */
    protected $_object;

    /**
     * @var \Magento\Core\Model\View\Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewUrl;

    protected function setUp()
    {
        $this->_viewUrl = $this->getMock('Magento\Core\Model\View\Url', array(), array(), '', false);
        $this->_object = new \Magento\Core\Model\Page\Asset\ViewFile($this->_viewUrl, 'test/script.js', 'js');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Parameter 'file' must not be empty
     */
    public function testConstructorException()
    {
        new \Magento\Core\Model\Page\Asset\ViewFile($this->_viewUrl, '', 'unknown');
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
        $this->_viewUrl
            ->expects($this->once())
            ->method('getViewFilePublicPath')
            ->with('test/script.js')
            ->will($this->returnValue($sourcePath))
        ;
        $this->assertEquals($sourcePath, $this->_object->getSourceFile());
    }
}
