<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Collection;

use Magento\TestFramework\Helper\Bootstrap;

class TaxRuleCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testCreateTaxRuleCollectionItem()
    {
        /** @var \Magento\Tax\Model\Resource\Calculation\Rule\Collection $collection */
        $collection = Bootstrap::getObjectManager()->get('Magento\Tax\Model\Resource\Calculation\Rule\Collection');
        $dbTaxRulesQty = $collection->count();
        if (($dbTaxRulesQty == 0) || ($collection->getFirstItem()->getId() != 1)) {
            $this->fail("Preconditions failed.");
        }
        /** @var \Magento\Tax\Service\V1\Collection\TaxRuleCollection $taxRulesCollection */
        $taxRulesCollection = Bootstrap::getObjectManager()
            ->create('Magento\Tax\Service\V1\Collection\TaxRuleCollection');
        $collectionTaxRulesQty = $taxRulesCollection->count();
        $this->assertEquals($dbTaxRulesQty, $collectionTaxRulesQty, 'Tax rules quantity is invalid.');
        $taxRule = $taxRulesCollection->getFirstItem()->getData();
        $expectedTaxRuleData = [
            'tax_calculation_rule_id' => '1',
            'code' => 'Test Rule',
            'priority' => '0',
            'position' => '0',
            'calculate_subtotal' => '0',
            'customer_tax_classes' => ['4', '5'],
            'product_tax_classes' => ['6', '7'],
            'tax_rates' => ['3'],
        ];
        $this->assertEquals($expectedTaxRuleData, $taxRule, 'Tax rule data is invalid.');
    }
}