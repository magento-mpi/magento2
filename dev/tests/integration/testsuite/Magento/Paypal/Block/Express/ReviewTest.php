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
    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     */
    public function testRenderAddress()
    {
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');

        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Paypal\Block\Express\Review'
        );
        $addressData = include __DIR__ . '/../../../Sales/_files/address_data.php';
        $address = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\Quote\Address',
            array('data' => $addressData)
        );
        $address->setAddressType('billing');
        $address->setQuote($quote);
        $this->assertContains('Los Angeles', $block->renderAddress($address));
    }
}
