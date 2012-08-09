<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ACL
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
  * Helper class
  *
  * @package     selenium
  * @subpackage  tests
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Enterprise2_Mage_PriceRules_Helper extends Core_Mage_PriceRules_Helper
{
    /**
     * Create Automated Email Reminder Rule
     */
    public function createEmailReminderRule()
    {
        //Data
        $ruleData = array ('rule_name' => $this->generate('text', '10'));
        //Steps
        $this->clickButton('add_new_rule');
        $this->validatePage('create_automated_email_reminder_rule');
        $this->fillFieldset($ruleData,'general_information');
        $this->saveForm('save_rule');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
    }
}