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

class PublicFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\PublicFile
     */
    protected $_object;

    /**
     * @var \Magento\View\UrlResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlResolver;

    protected function setUp()
    {
        $this->_urlResolver = $this->getMock('\Magento\View\UrlResolver', array(), array(), '', false);
        $this->_object = new \Magento\View\Asset\PublicFile($this->_urlResolver, 'test/style.css', 'css');
    }

    public function testGetUrl()
    {
        $url = 'http://127.0.0.1/magento/test/style.css';
        $this->_urlResolver
            ->expects($this->once())
            ->method('getPublicFileUrl')
            ->with('test/style.css')
            ->will($this->returnValue($url))
        ;
        $this->assertEquals($url, $this->_object->getUrl());
    }

    public function testGetContentType()
    {
        $this->assertEquals('css', $this->_object->getContentType());
    }

    public function testGetSourceFile()
    {
        $this->assertSame('test/style.css', $this->_object->getSourceFile());
    }
}
