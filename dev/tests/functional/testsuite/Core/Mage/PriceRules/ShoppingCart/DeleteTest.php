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
 * Deleting Rules
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_PriceRules_ShoppingCart_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Manage Shopping Cart Price Rules</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_shopping_cart_price_rules');
    }

    /**
     * <p>Delete Shopping cart price rule.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Create properly configured price rule (inactive) for shopping cart;</p>
     * <p>3. Open newly created shopping cart price rule;</p>
     * <p>4. Delete newly created shopping cart price rule;</p>
     * <p>Expected results:</p>
     * <p>Shopping Cart Price Rule successfully created and deleted;</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3321
     */
    public function deleteShoppingCartPriceRule()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        $ruleSearch = $this->loadDataSet('ShoppingCartPriceRule', 'search_shopping_cart_rule',
            array('filter_rule_name'   => $ruleData['info']['rule_name'],
                  'filter_coupon_code' => $ruleData['info']['coupon_code']));
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->priceRulesHelper()->deleteRule($ruleSearch);
        $this->assertMessagePresent('success', 'success_deleted_rule');
    }
}
