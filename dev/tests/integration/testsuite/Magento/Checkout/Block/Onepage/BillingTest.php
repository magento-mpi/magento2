<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Onepage;

use Magento\TestFramework\Helper\Bootstrap;

class BillingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Billing
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Checkout\Block\Onepage\Billing');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetAddressesHtmlSelect()
    {
        Bootstrap::getObjectManager()->get('Magento\Customer\Model\Session')->setCustomerId(1);
        $expected = <<<OUTPUT
<select name="billing_address_id" id="billing-address-select" class="address-select" title="" ><option value="1" selected="selected" >John Smith, Green str, 67, CityM, Alabama 75477, United States</option><option value="" >New Address</option></select>
OUTPUT;
        $this->assertEquals($expected, $this->_block->getAddressesHtmlSelect('billing'));
    }
}
