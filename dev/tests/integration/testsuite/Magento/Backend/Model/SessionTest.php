<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

/**
 * Test class for \Magento\Backend\Model\Session.
 *
 * @magentoAppArea adminhtml
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    public function testContructor()
    {
        if (array_key_exists('adminhtml', $_SESSION)) {
            unset($_SESSION['adminhtml']);
        }
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Backend\Model\Session',
            array($logger)
        );
        $this->assertArrayHasKey('adminhtml', $_SESSION);
    }
}
