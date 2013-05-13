<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page_Asset_ViewFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Page_Asset_ViewFile
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designPackage;

    protected function setUp()
    {
        $this->_designPackage = $this->getMock('Mage_Core_Model_Design_PackageInterface');
        $this->_object = new Mage_Core_Model_Page_Asset_ViewFile($this->_designPackage, 'test/script.js', 'js');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Parameter 'file' must not be empty
     */
    public function testConstructorException()
    {
        new Mage_Core_Model_Page_Asset_ViewFile($this->_designPackage, '', 'unknown');
    }

    public function testGetUrl()
    {
        $url = 'http://127.0.0.1/magento/test/script.js';
        $this->_designPackage
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
        $this->assertEquals('test/script.js', $this->_object->getSourceFile());
    }
}
