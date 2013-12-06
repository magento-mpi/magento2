<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase\CatalogPriceRule;

use Magento\Catalog\Test\Repository\SimpleProduct;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Mtf\Client\Element\Locator;

/**
 * Class ApplyCustomerGroupCatalogRule
 *
 * @package Magento\CatalogRule\Test\TestCase\CatalogPriceRule
 */
class ApplyCustomerGroupCatalogRule extends Functional
{
    /**
     * Applying Catalog Price Rules to specific customer group
     *
     * @ZephyrId MAGETWO-12908
     */
    public function testApplyCustomerGroupCatalogRule()
    {
        // Create Simple Product
        $simpleProductFixture = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simpleProductFixture->switchData(SimpleProduct::NEW_CATEGORY);
        $simpleProductFixture->persist();
        $categoryIds = $simpleProductFixture->getCategoryIds();
        // Create Customer
        $customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customerFixture->switchData('customer_US_1');
        $customerFixture->persist();

        // Create Customer Group Catalog Price Rule
        // Admin login
        Factory::getApp()->magentoBackendLoginUser();

        // Add Customer Group Catalog Price Rule
        $catalogRulePage = Factory::getPageFactory()->getCatalogRulePromoCatalog();
        $catalogRulePage->open();
        $catalogRuleGrid = $catalogRulePage->getCatalogPriceRuleGridBlock();
        $catalogRuleGrid->addNewCatalogRule();

        // Fill and Save the Form
        $catalogRuleCreatePage = Factory::getPageFactory()->getCatalogRulePromoCatalogNew();
        $newCatalogRuleForm = $catalogRuleCreatePage->getCatalogPriceRuleForm();
        $catalogRuleFixture = Factory::getFixtureFactory()->getMagentoCatalogRuleCatalogPriceRule();
        $catalogRuleFixture->setPlaceHolders(['category_id' => $categoryIds[0]]);
        $catalogRuleFixture->switchData('customer_group_catalog_rule');
        $newCatalogRuleForm->fill($catalogRuleFixture);
        $newCatalogRuleForm->save();

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->assertSuccessMessage();

        // Verify Notice Message
        $messagesBlock->assertNoticeMessage();

        // Verify Catalog Price Rule in grid
        $catalogRulePage->open();
        $gridBlock = $catalogRulePage->getCatalogPriceRuleGridBlock();
        $gridRow = $gridBlock->getRow(array('name' => $catalogRuleFixture->getRuleName()));
        $this->assertTrue(
            $gridRow->isVisible(),
            'Rule name "' . $catalogRuleFixture->getRuleName() . '" not found in the grid'
        );

        // Apply Catalog Price Rule
        $catalogRulePage->applyRules();

        // Verify Success Message
        $messagesBlock = $catalogRulePage->getMessagesBlock();
        $messagesBlock->assertSuccessMessage();
    }
}
