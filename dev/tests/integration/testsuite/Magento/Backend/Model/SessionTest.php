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

/**
 * Test class for \Magento\Backend\Model\Session.
 *
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Model_SessionTest extends PHPUnit_Framework_TestCase
{
    public function testContructor()
    {
        if (array_key_exists('adminhtml', $_SESSION)) {
            unset($_SESSION['adminhtml']);
        }
        $logger = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false);
        \Mage::getModel('Magento\Backend\Model\Session', array($logger));
        $this->assertArrayHasKey('adminhtml', $_SESSION);
    }
}
