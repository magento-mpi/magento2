<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\TaxClass\Source;

use Magento\TestFramework\Helper\Bootstrap;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllOptions()
    {
        /** @var \Magento\Tax\Model\Resource\TaxClass\Collection $collection */
        $collection = Bootstrap::getObjectManager()->get('Magento\Tax\Model\Resource\TaxClass\Collection');
        $expectedResult = [];
        /** @var \Magento\Tax\Model\ClassModel $taxClass */
        foreach ($collection as $taxClass) {
            if ($taxClass->getClassType() == \Magento\Tax\Service\V1\Data\TaxClass::TYPE_PRODUCT){
                $expectedResult[] = ['value' => $taxClass->getId(), 'label' => $taxClass->getClassName()];
            }
        }
        if (empty($expectedResult)) {
            $this->fail('Preconditions failed: At least one tax class should be available.');
        }
        /** @var \Magento\Tax\Model\TaxClass\Source\Product $source */
        $source = Bootstrap::getObjectManager()->get('Magento\Tax\Model\TaxClass\Source\Product');
        $this->assertEquals(
            $expectedResult,
            $source->getAllOptions(false),
            'Tax Class options are invalid.'
        );
    }

    public function testGetAllOptionsWithDefaultValues()
    {
        /** @var \Magento\Tax\Model\Resource\TaxClass\Collection $collection */
        $collection = Bootstrap::getObjectManager()->get('Magento\Tax\Model\Resource\TaxClass\Collection');
        $expectedResult = [];
        /** @var \Magento\Tax\Model\ClassModel $taxClass */
        foreach ($collection as $taxClass) {
            if ($taxClass->getClassType() == \Magento\Tax\Service\V1\Data\TaxClass::TYPE_PRODUCT){
                $expectedResult[] = ['value' => $taxClass->getId(), 'label' => $taxClass->getClassName()];
            }
        }
        if (empty($expectedResult)) {
            $this->fail('Preconditions failed: At least one tax class should be available.');
        }
        $expectedResult = array_merge(array(array('value' => '0', 'label' => __('None'))), $expectedResult);
        /** @var \Magento\Tax\Model\TaxClass\Source\Product $source */
        $source = Bootstrap::getObjectManager()->get('Magento\Tax\Model\TaxClass\Source\Product');
        $this->assertEquals(
            $expectedResult,
            $source->getAllOptions(true),
            'Tax Class options are invalid.'
        );
    }
}