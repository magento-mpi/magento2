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

namespace Magento\Core\Helper;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\Http
     */
    protected $_helper = null;

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Helper\Http');
    }

    public function testGetServerAddr()
    {
        $this->assertEquals(false, $this->_helper->getServerAddr());
    }

    public function testGetRequestUri()
    {
        $this->assertNull($this->_helper->getRequestUri());
    }

    public function testValidateIpAddr()
    {
        $this->assertTrue((bool)$this->_helper->validateIpAddr('127.0.0.1'));
        $this->assertFalse((bool)$this->_helper->validateIpAddr('invalid'));
    }
}
