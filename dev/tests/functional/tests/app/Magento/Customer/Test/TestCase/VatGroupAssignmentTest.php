<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Customer\Test\Fixture\VatGroup;
use Magento\Customer\Test\Page;

/**
 * Enabling automatic assignment of customers to appropriate VAT group
 */
class VatGroupAssignmentTest extends Functional
{
    /**
     * Vat fixture
     *
     * @var VatGroup
     */
    protected $vatFixture;

    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
        $this->vatFixture = Factory::getFixtureFactory()->getMagentoCustomerVatGroup();
    }

    /**
     * Enabling automatic assignment of customers to appropriate VAT group
     *
     * @ZephyrId MAGETWO-12447
     */
    public function testEnableCustomerVatAssignment()
    {
        // Data
        $customerLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customersPage = Factory::getPageFactory()->getCustomerIndex();
        $customerAccountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $customerDefaultAddressesPage = Factory::getPageFactory()->getCustomerAddressIndex();
        $customerAddressEditPage = Factory::getPageFactory()->getCustomerAddressEdit();

        $this->vatFixture->persist();
        $customersPage->open();
        $this->checkCustomerGroup($customersPage, $this->vatFixture->getDefaultCustomerGroup());

        $customerLoginPage->open();
        $customerLoginPage->getLoginBlock()->login($this->vatFixture->getCustomer());
        $customerAccountIndexPage->getAccountMenuBlock()->openMenuItem('Address Book');
        $customerDefaultAddressesPage->getDefaultAddresses()->goToAddressBook();
        $this->fillVatId($customerAddressEditPage, $this->vatFixture->getInvalidVatNumber());

        $this->checkCustomerGroup($customersPage, $this->vatFixture->getInvalidVatGroup());

        $customerAccountIndexPage->open();
        $customerAccountIndexPage->getAccountMenuBlock()->openMenuItem('Address Book');
        $customerDefaultAddressesPage->getDefaultAddresses()->goToAddressBook();
        $this->fillVatId($customerAddressEditPage, $this->vatFixture->getValidVatNumber());

        $this->checkCustomerGroup($customersPage, $this->vatFixture->getValidVatIntraUnionGroup());
    }

    /**
     * Check customer group in grid
     *
     * @param Page\Adminhtml\CustomerIndex $page
     * @param $groupName
     */
    protected function checkCustomerGroup(Page\Adminhtml\CustomerIndex $page, $groupName)
    {
        $page->open();
        $grid = $page->getCustomerGridBlock();
        $email = $this->vatFixture->getCustomer()->getEmail();
        $this->assertTrue(
            $grid->isRowVisible(
                [
                    'email' => $email,
                    'group' => $groupName,
                ]
            )
        );
    }

    /**
     * Fill and Save form with new VAT
     *
     * @param Page\CustomerAddressEdit $page
     * @param string $vatId
     */
    protected function fillVatId(Page\CustomerAddressEdit $page, $vatId)
    {
        $form = $page->getEditForm();
        $form->saveVatID($vatId);
    }

    /**
     * Remove created customer groups and disable automatic group assign
     */
    protected function tearDown()
    {
        if ($this->vatFixture instanceof VatGroup) {
            Factory::getApp()->magentoCustomerRemoveCustomerGroup($this->vatFixture);
        }

        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('customer_disable_group_assign');
        $config->persist();
    }
}
