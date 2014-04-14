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
class Enterprise_Mage_PriceRules_Helper extends Core_Mage_PriceRules_Helper
{
    /**
     * Create Automated Email Reminder Rule
     */
    public function createEmailReminderRule(array $ruleData)
    {
        $this->clickButton('add_new_rule');
        $this->fillTabs($ruleData);
        $this->saveForm('save_rule');
    }

    /**
     * Create and apply new Rule
     *
     * @param array $createRuleData
     */
    public function createAndApplyRule(array $createRuleData)
    {
        $this->clickButton('add_new_rule');
        $this->fillTabs($createRuleData);
        $this->saveForm('save_and_apply');
    }
}

