<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_StoreLauncher
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bussines Info Drawer tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_StoreLauncher_StoreInfo_DrawerTest extends Mage_Selenium_TestCase
{
    /**
     * Restore flag
     *
     * @var bool
     */
    protected $_restoreRequired = false;

    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Store Launcher page</p>
     */
    protected function assertPreConditions()
    {
        $this->currentWindow()->maximize();
        $this->loginAdminUser();
        $this->navigate('store_launcher');
    }

    /**
     * Restore settings
     */
    protected function tearDownAfterTest()
    {
        if ($this->_restoreRequired) {
            $this->loginAdminUser();
            $this->navigate('system_configuration');
            $config = $this->loadDataSet('ShippingSettings', 'store_information_empty');
            $this->systemConfigurationHelper()->configure($config);
            $config = $this->loadDataSet('General', 'general_default_emails');
            $this->systemConfigurationHelper()->configure($config);
            $this->_restoreRequired = false;
        }
    }

    /**
     * <p>Store Info drawer is displayed</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6504
     */
    public function storeInfoDrawerIsDisplayed()
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('bussines_info_tile');
        $this->assertTrue($this->controlIsVisible('fieldset', 'bussines_info_drawer_form'));
        $this->assertTrue($this->controlIsVisible('button', 'close_drawer'));
        $this->assertTrue($this->controlIsVisible('button', 'save_my_settings'));
    }

    /**
     * <p>User can return to the Store Launcher page</p>
     *
     * @depends storeInfoDrawerIsDisplayed
     * @test
     * @TestlinkId TL-MAGE-6506
     */
    public function returnToTheStoreLauncherPage()
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('bussines_info_tile');
        $this->assertTrue($helper->closeDrawer(), 'Failed to close drawer');
    }

    /**
     * <p>User can edit Business Info information.</p>
     *
     * @depends storeInfoDrawerIsDisplayed
     * @test
     * @TestlinkId TL-MAGE-6508
     */
    public function editBusinessInfoInformation()
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('bussines_info_tile');

        $data = $this->loadDataSet('StoreInfo', 'store_info');
        $this->fillFieldset($data, 'bussines_info_drawer_form');
        $helper->saveDrawer();
        $this->_restoreRequired = true;

        $this->assertTrue($this->controlIsVisible('button', 'edit_store_info'), 'Tile state is not changed');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('general_general');
        $this->systemConfigurationHelper()->expandFieldSet('store_information');
        $this->assertTrue($this->verifyForm($data, 'general_general', array('store_contact_email')),
            $this->getParsedMessages());

        $this->systemConfigurationHelper()->openConfigurationTab('general_store_email_addresses');
        $this->systemConfigurationHelper()->expandFieldSet('general_contact');
        $this->assertTrue($this->verifyForm(array('general_sender_email' => $data['store_contact_email']),
                'general_store_email_addresses'), $this->getParsedMessages());
    }

    /**
     * <p>Business Address is displayed on tile after saving info on drawer</p>
     *
     * @depends storeInfoDrawerIsDisplayed
     * @test
     * @TestlinkId TL-MAGE-6509
     */
    public function businessAddressIsDisplayedOnTile()
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('bussines_info_tile');

        $data = $this->loadDataSet('StoreInfo', 'store_info');
        $this->fillFieldset($data, 'bussines_info_drawer_form');
        $helper->saveDrawer();
        $this->_restoreRequired = true;

        $this->assertTrue($this->controlIsVisible('button', 'edit_store_info'), 'Tile state is not changed');
        unset($data['store_name']);
        unset($data['store_contact_telephone']);
        unset($data['billing_vat_number']);
        foreach ($data as $key => $value)
        {
            $this->addParameter('addressData', $value);
            if (!$this->controlIsVisible('pageelement', 'complete_state_text')) {
                $this->addVerificationMessage("Displayed data is invalid. There is no '$value' on Tile");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>User can cancel editing Store Info</p>
     *
     * @depends storeInfoDrawerIsDisplayed
     * @test
     * @TestlinkId TL-MAGE-6510
     */
    public function cancelEditingStoreInfo()
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('bussines_info_tile');

        $data = $this->loadDataSet('StoreInfo', 'store_info');
        $this->fillFieldset($data, 'bussines_info_drawer_form');
        $helper->closeDrawer();

        $helper->openDrawer('bussines_info_tile');
        $emptyData = $this->loadDataSet('StoreInfo', 'store_info_default');
        $this->assertTrue($this->verifyForm($emptyData), $this->getParsedMessages());
    }

    /**
     * <p>All Store Email Addresses contains Store Contact Email by default</p>
     *
     * @depends storeInfoDrawerIsDisplayed
     * @test
     * @TestlinkId TL-MAGE-6527
     */
    public function editEmailAddresses()
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('bussines_info_tile');

        $email = $this->generate('email', 15, 'valid');
        $data = $this->loadDataSet('StoreInfo', 'store_info_email', null, array('email' => $email));
        $this->clickControl('pageelement', 'add_store_email_addressess', false);
        $this->waitForElementVisible($this->_getControlXpath('pageelement', 'additional_content'));
        $this->fillFieldset($data, 'bussines_info_drawer_form');
        $helper->saveDrawer();
        $this->_restoreRequired = true;

        $helper->openDrawer('bussines_info_tile');
        $validateData = $this->loadDataSet('StoreInfo', 'store_info_email_validate', null, array('email' => $email));
        $this->assertTrue($this->verifyForm($validateData), $this->getParsedMessages());

        $email = $this->generate('email', 15, 'valid');
        $validateData = $this->loadDataSet('StoreInfo', 'store_info_email_validate', null, array('email' => $email));
        $this->fillField('store_contact_email', $email);
        $this->assertTrue($this->verifyForm($validateData), $this->getParsedMessages());
    }
}