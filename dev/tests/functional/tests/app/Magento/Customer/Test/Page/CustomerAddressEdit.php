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

namespace Magento\Customer\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Customer\Test\Block\Address\Edit;

/**
 * Customer Address Edit page.
 *
 * @package Magento\Customer\Test\Page
 */
class CustomerAddressEdit extends Page
{
    /**
     * URL for Customer Address Edit page
     */
    const MCA = 'customer/address/edit';

    /**
     * Customer Address Edit form
     *
     * @var Edit
     */
    private $editForm;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;

        $this->editForm = Factory::getBlockFactory()->getMagentoCustomerAddressEdit(
            $this->_browser->find('form-validate', Locator::SELECTOR_ID)
        );
    }

    /**
     * Get Customer Address Edit form
     *
     * @return Edit
     */
    public function getEditForm()
    {
        return $this->editForm;
    }
}
