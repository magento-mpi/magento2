<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\HTTP\PhpEnvironment;

class ServerAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\ServerAddress
     */
    protected $_helper;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_helper = $objectManager->get('Magento\Framework\HTTP\PhpEnvironment\ServerAddress');
    }

    public function testGetServerAddress()
    {
        $this->assertEquals(false, $this->_helper->getServerAddress());
    }
}
