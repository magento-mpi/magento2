<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf('Magento_Backend_Helper_Data', Mage::helper('Magento_Adminhtml_Helper_Data'));
    }
}
