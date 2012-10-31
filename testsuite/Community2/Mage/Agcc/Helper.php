<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Agcc
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
class Community2_Mage_Agcc_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create new Rule and Continue edit
     *
     * @param array $ruleData
     */
    public function createRuleAndContinueEdit($ruleData)
    {
        $this->addParameter('elementTitle', $ruleData['info']['rule_name']);
        $this->clickButton('add_new_rule');
        $this->priceRulesHelper()->fillTabs($ruleData);
        $this->saveForm('save_and_continue_edit');
    }
}