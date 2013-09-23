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
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Payment_Helper_Data');
        /** @var $paymentInfo Magento_Payment_Model_Info */
        $paymentInfo = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Payment_Model_Info');
        $paymentInfo->setMethod('checkmo');
        $result = $block->getInfoBlock($paymentInfo);
        $this->assertInstanceOf('Magento_Payment_Block_Info_Checkmo', $result);
    }
}
