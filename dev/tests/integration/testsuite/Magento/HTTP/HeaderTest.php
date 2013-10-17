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
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\HTTP\Header');
    }

    public function testGetHttpHeaderMethods()
    {
        $host = 'localhost';
        $this->assertEquals($host, $this->_helper->getHttpHost());
        $this->assertEquals(false, $this->_helper->getHttpUserAgent());
        $this->assertEquals(false, $this->_helper->getHttpAcceptLanguage());
        $this->assertEquals(false, $this->_helper->getHttpAcceptCharset());
        $this->assertEquals(false, $this->_helper->getHttpReferer());
    }
}
