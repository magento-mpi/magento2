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

class Magento_Core_Model_Page_Asset_PublicFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Page\Asset\PublicFile
     */
    protected $_object;

    /**
     * @var \Magento\Core\Model\View\Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewUrl;

    protected function setUp()
    {
        $this->_viewUrl = $this->getMock('Magento\Core\Model\View\Url', array(), array(), '', false);
        $this->_object = new \Magento\Core\Model\Page\Asset\PublicFile($this->_viewUrl, 'test/style.css', 'css');
    }

    public function testGetUrl()
    {
        $url = 'http://127.0.0.1/magento/test/style.css';
        $this->_viewUrl
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
