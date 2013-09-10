<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Payment_Helper_Data
 */
class Magento_Payment_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function testGetInfoBlock()
    {
        $helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Payment_Helper_Data');
        $paymentInfo = Mage::getModel('Magento_Payment_Model_Info');
        $paymentInfo->setMethod('checkmo');
        $result = $helper->getInfoBlock($paymentInfo);
        $this->assertInstanceOf('Magento_Payment_Block_Info_Checkmo', $result);
    }
}
