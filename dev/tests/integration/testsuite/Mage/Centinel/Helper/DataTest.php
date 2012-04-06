<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Centinel
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Centinel_Helper_Data
 */
class Mage_Centinel_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function testGetInfoBlock()
    {
        $block = new Mage_Payment_Helper_Data();
        $paymentInfo = new Mage_Payment_Model_Info;
        $paymentInfo->setMethod('checkmo');
        $result = $block->getInfoBlock($paymentInfo);
        $this->assertInstanceOf('Mage_Payment_Block_Info_Checkmo', $result);
    }
}
