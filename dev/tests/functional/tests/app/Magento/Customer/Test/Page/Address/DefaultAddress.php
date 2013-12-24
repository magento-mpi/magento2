<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page\Address;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

class DefaultAddress extends Page
{
    /**
     * URL for customer Dashboard
     */
    const MCA = 'customer/address/index';

    /**
     * Selector for default address block
     *
     * @var string
     */
    protected $defaultAddressesSelector = '.column.main .default div.content';

    /**
     * Get default addresses block
     *
     * @return \Magento\Customer\Test\Block\Account\AddressesDefault
     */
    public function getDefaultAddresses()
    {
        return Factory::getBlockFactory()->getMagentoCustomerAccountAddressesDefault(
            $this->_browser->find($this->defaultAddressesSelector, Locator::SELECTOR_CSS)
        );
    }
}
