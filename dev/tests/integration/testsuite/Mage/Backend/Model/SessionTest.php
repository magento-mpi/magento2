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
 * Test class for Mage_Backend_Model_Session.
 *
 * @magentoAppArea adminhtml
 */
class Mage_Backend_Model_SessionTest extends PHPUnit_Framework_TestCase
{
    public function testContructor()
    {
        if (array_key_exists('adminhtml', $_SESSION)) {
            unset($_SESSION['adminhtml']);
        }
        Mage::getModel('Mage_Backend_Model_Session');
        $this->assertArrayHasKey('adminhtml', $_SESSION);
    }
}
