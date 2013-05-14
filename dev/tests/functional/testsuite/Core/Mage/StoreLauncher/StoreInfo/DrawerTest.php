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
     * <p>Preconditions:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Store Launcher page</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $tileState = $this->getControlAttribute(self::UIMAP_TYPE_FIELDSET, 'bussines_info_tile', 'class');
        $changeState = ('tile-store-settings tile-store-info tile-complete' == $tileState) ? true : false;
        if ($changeState) {
            $this->navigate('system_configuration');
            $config = $this->loadDataSet('ShippingSettings', 'store_information_empty');
            $this->systemConfigurationHelper()->configure($config);
            $config = $this->loadDataSet('General', 'general_default_emails');
            $this->systemConfigurationHelper()->configure($config);
            $this->admin();
        }
    }

    /**
     * Set System Locale to default
     */
    public function tearDownAfterTest()
    {
            $this->loginAdminUser();
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure('General/general_locale_default');
    }

    /**
     * <p>User can edit Business Info information.</p>
     *
     * @param string $storeInfo Dataset name
     * @param string $locale Locale
     * @test
     * @TestlinkId TL-MAGE-6508
     * @dataProvider businessInfoDataProvider
     */
    public function editBusinessInfoInformation($storeInfo, $locale)
    {
        $this->navigate('system_configuration');
        $config = $this->loadDataSet('General', 'general_locale_default', array('locale' => $locale));
        $this->systemConfigurationHelper()->configure($config);
        $this->admin();
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('bussines_info_tile');

        $data = $this->loadDataSet('StoreInfo', $storeInfo);
        $this->fillFieldset($data, 'bussines_info_drawer_form');
        $helper->saveDrawer();

        $this->assertEquals('tile-store-settings tile-store-info tile-complete',
            $this->getControlAttribute(self::UIMAP_TYPE_FIELDSET, 'bussines_info_tile', 'class'),
            'Tile state is not Equal to Complete');
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
     * DataProvider for editBusinessInfoInformation()
     *
     * @return array
     */
    public function businessInfoDataProvider()
    {
        return array(
            array('store_info_no_vat', 'English (United States)'),
            array('store_info_vat', 'English (United Kingdom)'),
        );
    }

    /**
     * <p>Business Address is displayed on tile after saving info on drawer</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6509
     * @skipTearDown
     */
    public function businessAddressIsDisplayedOnTile()
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('bussines_info_tile');

        $data = $this->loadDataSet('StoreInfo', 'store_info_no_vat');
        $this->fillFieldset($data, 'bussines_info_drawer_form');
        $helper->saveDrawer();

        $validateData = $this->loadDataSet('StoreInfo', 'store_info_complete_validate', null,
                array('email' => $data['store_contact_email'], 'storeName' => $data['store_name']));
        foreach ($validateData as $value) {
            $this->addParameter('listInfo', $value);
            $helper->mouseOverDrawer('bussines_info_tile');
            if (!$this->controlIsVisible('pageelement', 'bussines_info_text')) {
                $this->addVerificationMessage("Displayed data is invalid. There is no '$value' on Tile");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>User can cancel editing Store Info</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6510
     * @skipTearDown
     */
    public function cancelEditingStoreInfo()
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('bussines_info_tile');

        $data = $this->loadDataSet('StoreInfo', 'store_info_no_vat');
        $this->fillFieldset($data, 'bussines_info_drawer_form');
        $helper->closeDrawer();

        $helper->openDrawer('bussines_info_tile');
        $emptyData = $this->loadDataSet('StoreInfo', 'store_info_default');
        $this->assertTrue($this->verifyForm($emptyData), $this->getParsedMessages());
    }

    /**
     * <p>All Store Email Addresses contains Store Contact Email by default</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6527
     * @skipTearDown
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

        $helper->openDrawer('bussines_info_tile');
        $validateData = $this->loadDataSet('StoreInfo', 'store_info_email_validate', null, array('email' => $email));
        $this->assertTrue($this->verifyForm($validateData), $this->getParsedMessages());

        $email = $this->generate('email', 15, 'valid');
        $validateData = $this->loadDataSet('StoreInfo', 'store_info_email_validate', null, array('email' => $email));
        $this->fillField('store_contact_email', $email);
        $this->assertTrue($this->verifyForm($validateData), $this->getParsedMessages());
    }
}