<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_PriceRules
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Price Rule creation
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_PriceRules_EmailReminder_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_automated_email_reminder_rules');
    }

    /**
     * <p>Create a new catalog price rule</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5676
     */
    public function requiredFields()
    {
        //Data
        $ruleData = array ('rule_name' => $this->generate('text', '10'));
        //Steps
        $this->clickButton('add_new_rule');
        $this->validatePage('create_automated_email_reminder_rule');
        $this->fillFieldset($ruleData, 'general_information');
        $this->saveForm('save_rule');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
    }
}
