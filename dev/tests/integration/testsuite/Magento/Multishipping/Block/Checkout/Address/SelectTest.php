<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Block\Checkout\Address;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppArea frontend
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Multishipping\Block\Checkout\Address\Select */
    protected $_selectBlock;

    protected function setUp()
    {
        $this->_selectBlock = Bootstrap::getObjectManager()->create(
            'Magento\Multishipping\Block\Checkout\Address\Select'
        );
        parent::setUp();
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetAddressAsHtml()
    {
        /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService */
        $addressService = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerAddressServiceInterface'
        );
        $fixtureAddressId = 1;
        $address = $addressService->getAddressById($fixtureAddressId);
        $addressAsHtml = $this->_selectBlock->getAddressAsHtml($address);
        $this->assertEquals(
            "John Smith&lt;br/&gt;Green str, 67&lt;br /&gt;CityM,  Alabama, 75477&lt;br/&gt;&lt;br/&gt;T: 3468676",
            str_replace("\n", '', $addressAsHtml),
            "Address was represented as HTML incorrectly"
        );
    }
}
