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
        $ruleData = array('rule_name' => $this->generate('text', '10'), 'assigned_to_website' => 'Main Website');
        //Steps
        $this->clickButton('add_new_rule');
        $this->validatePage('create_automated_email_reminder_rule');
        $this->fillFieldset($ruleData, 'general_information');
        $this->saveForm('save_rule');
    }

    /**
     * Create and apply new Rule
     *
     * @param array $createRuleData
     */
    public function createAndApplyRule($createRuleData)
    {
        if (empty($createRuleData)) {
            $this->fail('$roleData parameter is empty');
        }
        if (is_array($createRuleData)) {
            $this->clickButton('add_new_rule');
            $this->fillTabs($createRuleData);
            $this->saveForm('save_and_apply');
        }
    }

    /**
     * Save and Continue Edit new Rule
     *
     * @param array $createRuleData
     * @param string $pageToVerify
     */
    public function saveAndContinueEditRule($createRuleData, $pageToVerify)
    {
        if (!empty($pageToVerify) && is_string($pageToVerify)) {
            $this->clickButton('add_new_rule');
            $this->fillTabs($createRuleData);
            $this->saveForm('save_and_continue_edit', false);
            $this->addParameter('elementTitle', $createRuleData['info']['rule_name']);
            $this->validatePage($pageToVerify);

        }
    }
}

