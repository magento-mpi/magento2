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
namespace Magento\HTTP;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\HTTP\Header
     */
    protected $_header;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_header = $objectManager->get('Magento\HTTP\Header');

        /** @var \Magento\TestFramework\Request $request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $request->setServer(array('HTTP_HOST' => 'localhost'));
    }

    public function testGetHttpHeaderMethods()
    {
        $host = 'localhost';
        $this->assertEquals($host, $this->_header->getHttpHost());
        $this->assertEquals(false, $this->_header->getHttpUserAgent());
        $this->assertEquals(false, $this->_header->getHttpAcceptLanguage());
        $this->assertEquals(false, $this->_header->getHttpAcceptCharset());
        $this->assertEquals(false, $this->_header->getHttpReferer());
    }

    public function testGetRequestUri()
    {
        $this->assertNull($this->_header->getRequestUri());
    }
}
