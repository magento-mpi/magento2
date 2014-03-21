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
namespace Magento\HTTP\PhpEnvironment;

class RemoteAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_helper;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_helper = $objectManager->get('Magento\HTTP\PhpEnvironment\RemoteAddress');
    }

    public function testGetRemoteAddress()
    {
        $this->assertEquals(false, $this->_helper->getRemoteAddress());
    }
}
