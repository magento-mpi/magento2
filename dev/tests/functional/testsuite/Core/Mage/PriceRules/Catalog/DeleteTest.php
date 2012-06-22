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
 * Catalog Price Rule Delete
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_PriceRules_Catalog_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Promotions -> Catalog Price Rules</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_catalog_price_rules');
    }

    /**
     * <p>Delete Catalog Price Rule</p>
     * <p>PreConditions</p>
     * <p>New Catalog Price rule created</p>
     * <p>Steps</p>
     * <p>1. Open created Rule</p>
     * <p>2. Click "Delete Rule" button</p>
     * <p>3. Click "Ok" in confirmation window</p>
     * <p>4. Check confirmation message</p>
     *
     * <p>Expected result: Success message appears, rule removed from the list</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3314
     */
    public function deleteCatalogPriceRule()
    {
        //Data
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule');
        $ruleSearch = $this->loadDataSet('CatalogPriceRule', 'search_catalog_rule',
            array('filter_rule_name' => $priceRuleData['info']['rule_name']));
        //PreConditions
        $this->priceRulesHelper()->createRule($priceRuleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->assertMessagePresent('success', 'notification_message');
        //Steps
        $this->priceRulesHelper()->openRule($ruleSearch);
        $this->clickButtonAndConfirm('delete_rule', 'confirmation_for_delete');
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_rule');
    }
}