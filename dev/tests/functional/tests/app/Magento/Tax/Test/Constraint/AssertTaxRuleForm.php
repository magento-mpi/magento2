<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Constraint;

use Magento\Tax\Test\Fixture\TaxRule;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTaxRuleForm
 *
 * @package Magento\Tax\Test\Constraint
 */
class AssertTaxRuleForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that tax rule form filled right
     *
     * @param TaxRule $taxRule
     * @param TaxRuleNew $taxRuleNew
     * @param TaxRuleIndex $taxRuleIndex
     */
    public function processAssert(
        TaxRule $taxRule,
        TaxRuleNew $taxRuleNew,
        TaxRuleIndex $taxRuleIndex
    ) {
        $filter = [
            'code' => $taxRule->getCode(),
        ];
        $taxRuleIndex->open();
        $taxRuleIndex->getTaxRuleGrid()->searchAndOpen($filter);
        $taxRuleNew->getTaxRuleForm()->openAdditionalSettings();
        $formData = $taxRuleNew->getTaxRuleForm()->getData($taxRule);
        $fixtureData = $taxRule->getData();
        \PHPUnit_Framework_Assert::assertTrue(
            $this->verifyForm($formData, $fixtureData),
            'Tax Rule form was filled not right.'
        );
    }

    /**
     * Verifying that form is filled right
     *
     * @param array $formData
     * @param array $fixtureData
     * @return bool
     */
    protected function verifyForm(array $formData, array $fixtureData)
    {
        foreach ($fixtureData as $key => $value) {
            if (is_array($value)) {
                $diff = array_diff($value, $formData[$key]);
                if (!empty($diff)) {
                    return false;
                }
            } else {
                if ($value !== $formData[$key]) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Text that form was filled right
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax Rule form has been filled right.';
    }
}
