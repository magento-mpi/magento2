<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Model_SessionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf('Mage_Backend_Model_Session', Mage::getModel('Mage_Adminhtml_Model_Session'));
    }
}
