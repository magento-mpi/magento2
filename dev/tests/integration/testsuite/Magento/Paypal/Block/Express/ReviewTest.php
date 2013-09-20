<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Paypal\Block\Express\Review
 */
namespace Magento\Paypal\Block\Express;

class ReviewTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderAddress()
    {
        $block = \Mage::app()->getLayout()->createBlock('Magento\Paypal\Block\Express\Review');
        $addressData = include(__DIR__ . '/../../../Sales/_files/address_data.php');
        $address = \Mage::getModel('Magento\Sales\Model\Quote\Address', array('data' => $addressData));
        $address->setAddressType('billing');
        $this->assertContains('Los Angeles', $block->renderAddress($address));
    }
}
