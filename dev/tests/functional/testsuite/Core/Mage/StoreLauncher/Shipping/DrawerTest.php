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
class Core_Mage_StoreLauncher_Shipping_DrawerTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     */
    protected function assertPreConditions()
    {
        //Data
        $shippingConfig = $this->loadDataSet('ShippingMethod', 'shipping_disable');
        //Step
        $this->currentWindow()->maximize();
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($shippingConfig);
        $this->navigate('store_launcher');
    }

    /**
     * Restore shipping settings
     */
    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('store_launcher');
        $tileState = $this->getControlAttribute('fieldset', 'shipping_tile', 'class');
        $changeState = ('tile-store-settings tile-shipping tile-complete' == $tileState) ? true : false;
        if ($changeState) {
            $this->storeLauncherHelper()->setTileState('shipping', Core_Mage_StoreLauncher_Helper::$STATE_TODO);
        }
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
        $this->storeLauncherHelper()->mouseOverDrawer('shipping_tile');
        $this->assertTrue($this->controlIsVisible('button', 'open_shipping_drawer'), 'Tile state is changed');
        $this->assertEquals('tile-store-settings tile-shipping tile-todo',
            $this->getControlAttribute('fieldset', 'shipping_tile', 'class'), 'Tile state is not Equal to TODO');
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
        $this->storeLauncherHelper()->saveDrawer();
        //Verification
        $this->storeLauncherHelper()->mouseOverDrawer('shipping_tile');
        $this->assertTrue($this->controlIsVisible('button', 'configure_other_shipping_method'),
            'Tile state is not changed');
    }

    /**
     * <p>Configure shipping and save Drawer</p>
     *
     * @param string $shippingMethod
     * @test
     * @dataProvider shippingDataProvider
     * @TestlinkId TL-MAGE-6614
     */
    public function completeTile($shippingMethod)
    {
        //Data
        $data = $this->loadDataSet('ShippingDrawer', $shippingMethod);
        //Steps
        $this->assertEquals('tile-store-settings tile-shipping tile-todo',
            $this->getControlAttribute('fieldset', 'shipping_tile', 'class'), 'Tile state is not Equal to TODO');
        $this->storeLauncherHelper()->openDrawer('shipping_tile');
        $this->clickControl('pageelement', $shippingMethod . '_tab', false);
        $this->fillFieldset($data, 'shipping_drawer');
        $this->clickButton("submit_" . $shippingMethod, false);
        $this->waitForAjax();
        $this->storeLauncherHelper()->saveDrawer();
        //Verification
        $this->storeLauncherHelper()->mouseOverDrawer('shipping_tile');
        $this->assertTrue($this->controlIsVisible('button', 'configure_other_shipping_method'),
            'Tile state is not changed');
    }

    /**
     * Data for shippingDrawer
     *
     * @return array
     */
    public function shippingDataProvider()
    {
        return array(
            array('flat_rate'),
            array('fedex'),
            array('usps'),
            array('dhl'),
            array('ups')
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
        $this->navigate('store_launcher');
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
        $this->navigate('store_launcher');
        $this->storeLauncherHelper()->openDrawer('shipping_tile');
        $this->clickButton('edit_address', false);
        //Verification
        $this->assertTrue($this->verifyForm($validateAddress), $this->getParsedMessages());
    }
}
