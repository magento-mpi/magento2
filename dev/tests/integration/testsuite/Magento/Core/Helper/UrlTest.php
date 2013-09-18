<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Helper_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\Url
     */
    protected $_helper = null;

    protected function setUp()
    {
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Helper\Url');
    }

    public function testGetCurrentUrl()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/fancy_uri';
        $this->assertEquals('http://example.com/fancy_uri', $this->_helper->getCurrentUrl());
    }

    public function testGetCurrentBase64Url()
    {
        $this->assertEquals('aHR0cDovL2xvY2FsaG9zdA,,', $this->_helper->getCurrentBase64Url());
    }

    public function testGetEncodedUrl()
    {
        $this->assertEquals('aHR0cDovL2xvY2FsaG9zdA,,', $this->_helper->getEncodedUrl());
        $this->assertEquals('aHR0cDovL2V4YW1wbGUuY29tLw,,', $this->_helper->getEncodedUrl('http://example.com/'));
    }

    public function testGetHomeUrl()
    {
        $this->assertEquals('http://localhost/index.php/', $this->_helper->getHomeUrl());
    }
}
