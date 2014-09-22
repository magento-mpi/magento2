<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleIndex;

/**
 * Class DeleteTaxRule
 * Delete Tax Rule on backend
 */
class DeleteTaxRule implements TestStepInterface
{
    /**
     * Tax Rule grid page
     *
     * @var TaxRuleIndex
     */
    protected $taxRuleIndexPage;

    /**
     * Tax Rule new and edit page
     *
     * @var TaxRuleNew
     */
    protected $taxRuleNewPage;

    /**
     * @construct
     * @param TaxRuleIndex $taxRuleIndexPage
     * @param TaxRuleNew $taxRuleNewPage
     */
    public function __construct(
        TaxRuleIndex $taxRuleIndexPage,
        TaxRuleNew $taxRuleNewPage
    ) {
        $this->taxRuleIndexPage = $taxRuleIndexPage;
        $this->taxRuleNewPage = $taxRuleNewPage;
    }

    /**
     * Delete Tax Rule on backend
     *
     * @return array
     */
    public function run()
    {
        $this->taxRuleIndexPage->open();
        while ($this->taxRuleIndexPage->getTaxRuleGrid()->isFirstRowVisible()) {
            $this->taxRuleIndexPage->getTaxRuleGrid()->openFirstRow();
            $this->taxRuleNewPage->getFormPageActions()->delete();
        }
    }
}
