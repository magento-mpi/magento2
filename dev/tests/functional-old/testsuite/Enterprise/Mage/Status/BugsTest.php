<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Covering bugs
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Status_BugsTest extends Mage_Selenium_TestCase
{
    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('PersistentShoppingCart/disable_persistent_shopping_cart');
    }

    /**
     * <p>After clicking the "What's this?" link information text appears at the bottom of the page</p>
     * <p>Bug MAGETWO-1395</p>
     * <p>This test checks whether there is a message to the customer login page.
     *    The location messages can only check manually.</p>
     *
     * @test
     */
    public function afterClickingWhatIsThisLink()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('PersistentShoppingCart/enable_persistent_shopping_cart');
        $this->frontend('customer_login');
        $this->clickControl('link', 'what_is_this', false);
        $this->assertTrue(
            $this->controlIsVisible('fieldset', 'what_is_this'),
            'There is no "What is this?" on the page'
        );
        $this->clickButton('close', false);
    }
}
