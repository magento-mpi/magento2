<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\TestFramework\Helper\Bootstrap;

class TaxRateCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateTaxRateCollectionItem()
    {
        /** @var \Magento\Tax\Model\Resource\Calculation\Rate\Collection $collection */
        $collection = Bootstrap::getObjectManager()->get('Magento\Tax\Model\Resource\Calculation\Rate\Collection');
        $dbTaxRatesQty = $collection->count();
        if (($dbTaxRatesQty == 0) || ($collection->getFirstItem()->getId() != 1)) {
            $this->fail("Preconditions failed.");
        }
        /** @var \Magento\Tax\Service\V1\Data\TaxRateCollection $taxRatesCollection */
        $taxRatesCollection = Bootstrap::getObjectManager()->create('\Magento\Tax\Service\V1\Data\TaxRateCollection');
        $collectionTaxRatesQty = $taxRatesCollection->count();
        $this->assertEquals($dbTaxRatesQty, $collectionTaxRatesQty, 'Tax rates quantity is invalid.');
        $taxRate = $taxRatesCollection->getFirstItem()->getData();
        $expectedTaxRateData = [
            'code' => 'US-CA-*-Rate 1',
            'id' => '1',
            'rate' => '8.25',
            'region_name' => 'CA',
            'tax_country_id' => 'US',
            'tax_postcode' => '*'
        ];
        $this->assertEquals($taxRate, $expectedTaxRateData, 'Tax rate data is invalid.');
    }
}