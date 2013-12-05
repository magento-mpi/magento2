<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Customer\Test\Fixture\VatGroup;
use Magento\Customer\Test\Page;

/**
 * Enabling automatic assignment of customers to appropriate VAT group
 *
 * @package Magento\Customer\Test\TestCase;
 */
class VatGroupAssignmentTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Enabling automatic assignment of customers to appropriate VAT group
     *
     * @ZephyrId MAGETWO-12447
     */
    public function testEnableCustomerVatAssignment()
    {
        // Data
        $vatGroup = Factory::getFixtureFactory()->getMagentoCustomerVatGroup();
        $customerLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customersPage = Factory::getPageFactory()->getCustomer();
        $customerAccountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $customerDefaultAddressesPage = Factory::getPageFactory()->getCustomerAddressIndex();
        $customerAddressEditPage = Factory::getPageFactory()->getCustomerAddressEdit();

        $vatGroup->persist();
        $customersPage->open();
        $this->checkCustomerGroup($customersPage, $vatGroup, $vatGroup->getDefaultCustomerGroup());

        $customerLoginPage->open();
        $customerLoginPage->getLoginBlock()->login($vatGroup->getCustomer());
        $customerAccountIndexPage->getAccountMenuBlock()->goToAddressBook();
        $customerDefaultAddressesPage->getDefaultAddresses()->goToAddressBook();
        $this->fillVatId($customerAddressEditPage, $vatGroup, 'invalid');

        $this->checkCustomerGroup($customersPage, $vatGroup, $vatGroup->getInvalidVatCustomerGroup());

        $customerAccountIndexPage->open();
        $customerAccountIndexPage->getAccountMenuBlock()->goToAddressBook();
        $customerDefaultAddressesPage->getDefaultAddresses()->goToAddressBook();
        $this->fillVatId($customerAddressEditPage, $vatGroup, 'valid');

        $this->checkCustomerGroup($customersPage, $vatGroup, $vatGroup->getValidVatCustomerGroup());
    }

    /**
     * Check customer group in grid
     *
     * @param Page\Customer $page
     * @param VatGroup $vatFixture
     * @param $groupName\
     */
    protected function checkCustomerGroup(Page\Customer $page, VatGroup $vatFixture, $groupName)
    {
        $page->open();
        $grid = $page->getCustomerGridBlock();
        $email = $vatFixture->getCustomer()->getEmail();
        $this->assertEquals($groupName, $grid->getGroupByEmail($email));
    }

    /**
     * Fill and Save form with new VAT
     *
     * @param Page\CustomerAddressEdit $page
     * @param VatGroup $vatFixture
     * @param string $type
     */
    protected function fillVatId(Page\CustomerAddressEdit $page, VatGroup $vatFixture, $type)
    {
        $form = $page->getEditForm();
        $form->saveVatID($vatFixture->getVat($type));
    }
}
