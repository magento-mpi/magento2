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
 * Shipping Drawer tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_StoreLauncher_Shipping_DrawerTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Store Launcher page</p>
     * <p>3. Reset tile state</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->storeLauncherHelper()->resetShippingTile();
    }

    /**
     * <p>Tile status not change after save not configured Drawer</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6904
     */
    public function saveDrawerWithoutConfiguredShipment()
    {
        //Steps
        $this->assertEquals('tile-store-settings tile-shipping tile-todo',
            $this->getControlAttribute('fieldset', 'shipping_tile', 'class'), 'Tile state is not Equal to TODO');
        $this->storeLauncherHelper()->openDrawer('shipping_tile');
        $this->storeLauncherHelper()->saveDrawer();
        //Verification
        $this->assertEquals('tile-store-settings tile-shipping tile-todo',
            $this->getControlAttribute('fieldset', 'shipping_tile', 'class'), 'Tile state is not Equal to Complete');
    }

    /**
     * <p>Set no shipping options on Drawer</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6903
     */
    public function switchNoShipping()
    {
        //Steps
        $this->assertEquals('tile-store-settings tile-shipping tile-todo',
            $this->getControlAttribute('fieldset', 'shipping_tile', 'class'), 'Tile state is not Equal to TODO');
        $this->storeLauncherHelper()->openDrawer('shipping_tile');
        $this->clickControl('pageelement', 'shipping_switcher', false);
        $this->controlIsVisible('pageelement', 'shipping_disabled_content');
        $this->storeLauncherHelper()->saveDrawer();
        //Verification
        $this->assertEquals('tile-store-settings tile-shipping tile-complete',
            $this->getControlAttribute('fieldset', 'shipping_tile', 'class'), 'Tile state is not Equal to Complete');
    }

    /**
     * <p>Configure shipping and save Drawer</p>
     *
     * @param string $shippingMethod
     * @param string $configField
     * @test
     * @dataProvider shippingDataProvider
     * @TestlinkId TL-MAGE-6614
     */
    public function completeTile($shippingMethod, $configField)
    {
        //Data
        $data = $this->loadDataSet('ShippingDrawer', $shippingMethod);
        $validateData = array($configField => 'Yes');
        //Steps
        $this->assertEquals('tile-store-settings tile-shipping tile-todo',
            $this->getControlAttribute('fieldset', 'shipping_tile', 'class'), 'Tile state is not Equal to TODO');
        $this->storeLauncherHelper()->openDrawer('shipping_tile');
        $this->clickControl('pageelement', $shippingMethod . '_tab', false);
        $this->fillFieldset($data, 'shipping_drawer');
        $this->clickButton("submit_" . $shippingMethod, false);
        $this->waitForAjax();
        $this->assertMessagePresent('success', 'success_saved_shipping');
        $this->storeLauncherHelper()->saveDrawer();
        //Verification
        $this->assertEquals('tile-store-settings tile-shipping tile-complete',
            $this->getControlAttribute('fieldset', 'shipping_tile', 'class'), 'Tile state is not Equal to Complete');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('sales_shipping_methods');
        $this->systemConfigurationHelper()->expandFieldSet($shippingMethod);
        $this->assertTrue($this->verifyForm($validateData, 'sales_shipping_methods'), $this->getParsedMessages());
    }

    /**
     * Data for shippingDrawer
     *
     * @return array
     */
    public function shippingDataProvider()
    {
        return array(
            array('flat_rate', 'flat_rate_enabled'),
            array('fedex', 'fedex_enabled_for_checkout'),
            array('usps', 'usps_enabled_for_checkout'),
            array('dhl', 'dhl_enabled'),
            array('ups', 'ups_enabled')
        );
    }

    /**
     * <p>Save Shipping Origin address on Drawer</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6616
     */
    public function saveShippingOriginAddress()
    {
        //Data
        $defaultAddress = $this->loadDataSet('ShippingSettings', 'shipping_settings_default');
        $address = $this->loadDataSet('ShippingDrawer', 'sent_from_address');
        $validateAddress = $this->loadDataSet('ShippingSettings', 'validate_shipping_drawer');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($defaultAddress);
        $this->admin();
        $this->storeLauncherHelper()->openDrawer('shipping_tile');
        $this->fillFieldset($address, 'shipping_drawer');
        $this->clickButton('save_address', false);
        $this->storeLauncherHelper()->saveDrawer();
        //Verification
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('sales_shipping_settings');
        $this->systemConfigurationHelper()->expandFieldSet('origin');
        $this->assertTrue($this->verifyForm($validateAddress, 'sales_shipping_settings'), $this->getParsedMessages());
    }

    /**
     * <p>Shipping Origin address show on Drawer</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6905
     */
    public function shippingOriginAddressShowOnDrawer()
    {
        //Data
        $address = $this->loadDataSet('ShippingSettings', 'validate_shipping_drawer');
        $validateAddress = $this->loadDataSet('ShippingDrawer', 'sent_from_address');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($address);
        $this->admin();
        $this->storeLauncherHelper()->openDrawer('shipping_tile');
        $this->clickButton('edit_address', false);
        //Verification
        $this->assertTrue($this->verifyForm($validateAddress), $this->getParsedMessages());
    }
}
