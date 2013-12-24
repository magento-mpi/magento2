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

namespace Magento\Customer\Test\Handler\Ui;

use Mtf\Fixture;
use Mtf\Factory\Factory;

/**
 * UI handler for creating customer address.
 */
class CreateAddress extends \Mtf\Handler\Ui
{
    /**
     * Execute handler
     *
     * @param Fixture $fixture [optional]
     * @return mixed
     */
    public function execute(Fixture $fixture = null)
    {
        /** @var \Magento\Customer\Test\Fixture\Address $fixture */
        // Pages
        $loginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $addressPage = Factory::getPageFactory()->getCustomerAddressEdit();

        $loginPage->open();
        if ($loginPage->getLoginBlock()->isVisible()) {
            $loginPage->getLoginBlock()->login($fixture->getCustomer());
        }

        $addressPage->open();
        $addressPage->getEditForm()->editCustomerAddress($fixture);
    }
}
