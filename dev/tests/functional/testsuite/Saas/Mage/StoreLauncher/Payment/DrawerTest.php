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
 * Payments Drawer tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_StoreLauncher_Payment_DrawerTest extends Mage_Selenium_TestCase
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
        $this->storeLauncherHelper()->resetPaymentsTile();
    }

    /**
     * <p>Tile status not change after save not configured Drawer</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6865
     */
    public function saveDrawerWithoutConfiguredPayment()
    {
        //Steps
        $this->assertEquals('tile-store-settings tile-payments tile-todo',
            $this->getControlAttribute('fieldset', 'payment_tile', 'class'), 'Tile state is not Equal to TODO');
        $this->storeLauncherHelper()->openDrawer('payment_tile');
        $this->storeLauncherHelper()->saveDrawer();
        //Verification
        $this->assertEquals('tile-store-settings tile-payments tile-todo',
            $this->getControlAttribute('fieldset', 'payment_tile', 'class'), 'Tile state is changed');
    }

    /**
     * <p>Configure payment and save Drawer</p>
     *
     * @param string $paymentMethod
     * @param string $solutions
     * @test
     * @dataProvider paymentDataProvider
     * @TestlinkId TL-MAGE-6588, TL-MAGE-6590
     */
    public function completeTile($solutions, $paymentMethod)
    {
        //Data
        $data = $this->loadDataSet('PaymentTile', $paymentMethod);
        $this->addParameter('paymentMethod', $paymentMethod);

        //Steps
        $this->assertEquals('tile-store-settings tile-payments tile-todo',
            $this->getControlAttribute('fieldset', 'payment_tile', 'class'), 'Tile state is not Equal to TODO');
        $this->storeLauncherHelper()->openDrawer('payment_tile');
        $this->clickControl('link', $solutions, false);
        if ($paymentMethod == 'paypal_express') {
            $this->clickButton('choose_paypal_standard', false);
        }
        $this->clickButton("choose_" . $paymentMethod, false);
        $this->fillFieldset($data, 'payment_drawer');
        $this->clickButton("confirm_" . $paymentMethod, false);
        $this->waitForAjax();
        $this->controlIsVisible('pageelement', 'setup_seccessful');
        $this->storeLauncherHelper()->saveDrawer();
        //Verification
        $this->assertEquals('tile-store-settings tile-payments tile-complete',
            $this->getControlAttribute('fieldset', 'payment_tile', 'class'), 'Tile state is not changed');
    }

    /**
     * Data for paymentDrawer
     *
     * @return array
     */
    public function paymentDataProvider()
    {
        return array(
            array('solutions_for_everyone', 'paypal_express'),
            array('solutions_for_everyone', 'paypal_standard'),
            array('solutions_for_everyone', 'paypal_advanced'),
            array('solutions_for_everyone', 'paypal_payments_pro'),
            array('solutions_for_bank_accounts', 'paypal_payflow_link'),
            array('solutions_for_bank_accounts', 'paypal_payflow_pro'),
            array('solutions_for_bank_accounts', 'authorize')
        );
    }

    /**
     * <p>Button on completed Tile opens Payment tab in System Configuration</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6866
     */

    public function goToSystemConfigFromCompletedTile()
    {
        //Data
        $data = $this->loadDataSet('PaymentTile', 'paypal_express');
        $this->addParameter('paymentMethod', 'paypal_express');
        //Steps
        $this->assertEquals('tile-store-settings tile-payments tile-todo',
            $this->getControlAttribute('fieldset', 'payment_tile', 'class'), 'Tile state is not Equal to TODO');
        $this->storeLauncherHelper()->openDrawer('payment_tile');
        $this->fillFieldset($data, 'payment_drawer');
        $this->clickButton('confirm_paypal_express', false);
        $this->waitForAjax();
        $this->controlIsVisible('pageelement', 'setup_seccessful');
        $this->storeLauncherHelper()->saveDrawer();
        //Verification
        $tileElement = $this->storeLauncherHelper()->mouseOverDrawer('payment_tile');
        $action = $this->getChildElement($tileElement,
            $this->_getControlXpath(self::FIELD_TYPE_LINK, 'additional_action'));
        $this->assertNotNull($action, 'Could not find "Additional action link"');
        $action->click();
        $this->addParameter('tabName', 'sales_payment_methods');
        $this->validatePage('system_configuration_tabs');
    }
}
