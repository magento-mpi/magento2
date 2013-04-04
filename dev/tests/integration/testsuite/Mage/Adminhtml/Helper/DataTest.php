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

class Mage_Adminhtml_Helper_DataTest extends Mage_Backend_Area_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf('Mage_Backend_Helper_Data', Mage::helper('Mage_Adminhtml_Helper_Data'));
    }
}
