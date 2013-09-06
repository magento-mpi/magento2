<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Centinel_Helper_Data
 */
class Magento_Centinel_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function testGetInfoBlock()
    {
        /** @var $block Magento_Payment_Helper_Data */
        $block = Mage::helper('Magento_Payment_Helper_Data');
        /** @var $paymentInfo Magento_Payment_Model_Info */
        $paymentInfo = Mage::getModel('Magento_Payment_Model_Info');
        $paymentInfo->setMethod('checkmo');
        $result = $block->getInfoBlock($paymentInfo);
        $this->assertInstanceOf('Magento_Payment_Block_Info_Checkmo', $result);
    }
}
