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
 * Test class for \Magento\Centinel\Helper\Data
 */
class Magento_Centinel_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function testGetInfoBlock()
    {
        /** @var $block \Magento\Payment\Helper\Data */
        $block = Mage::helper('Magento\Payment\Helper\Data');
        /** @var $paymentInfo \Magento\Payment\Model\Info */
        $paymentInfo = Mage::getModel('Magento\Payment\Model\Info');
        $paymentInfo->setMethod('checkmo');
        $result = $block->getInfoBlock($paymentInfo);
        $this->assertInstanceOf('Magento\Payment\Block\Info\Checkmo', $result);
    }
}
