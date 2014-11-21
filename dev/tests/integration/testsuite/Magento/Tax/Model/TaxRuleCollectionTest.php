<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model;

use Magento\TestFramework\Helper\Bootstrap;

class TaxRuleCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testCreateTaxRuleCollectionItem()
    {
        /** @var \Magento\Tax\Model\Resource\Calculation\Rule\Collection $collection */
        $collection = Bootstrap::getObjectManager()->get('Magento\Tax\Model\Resource\Calculation\Rule\Collection');
        $dbTaxRulesQty = $collection->count();

        $firstTaxRuleFixture = Bootstrap::getObjectManager()->get('Magento\Framework\Registry')
            ->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $expectedFirstTaxRuleId = $firstTaxRuleFixture->getId();

        if (($dbTaxRulesQty == 0) || ($collection->getFirstItem()->getId() != $expectedFirstTaxRuleId)) {
            $this->fail("Preconditions failed.");
        }
        /** @var \Magento\Tax\Model\TaxRuleCollection $taxRulesCollection */
        $taxRulesCollection = Bootstrap::getObjectManager()
            ->create('Magento\Tax\Model\TaxRuleCollection');
        $collectionTaxRulesQty = $taxRulesCollection->count();
        $this->assertEquals($dbTaxRulesQty, $collectionTaxRulesQty, 'Tax rules quantity is invalid.');
        $taxRule = $taxRulesCollection->getFirstItem()->getData();
        $expectedTaxRuleData = [
            'tax_calculation_rule_id' => $expectedFirstTaxRuleId,
            'code' => 'Test Rule',
            'priority' => '0',
            'position' => '0',
            'calculate_subtotal' => '0',
            'customer_tax_classes' => $firstTaxRuleFixture->getTaxCustomerClass(),
            'product_tax_classes' => $firstTaxRuleFixture->getTaxProductClass(),
            'tax_rates' => $firstTaxRuleFixture->getTaxRate()
        ];

        $this->assertEquals($expectedTaxRuleData, $taxRule, 'Tax rule data is invalid.');
    }
}
