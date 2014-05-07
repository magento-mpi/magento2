<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\TestCase;

use Magento\Tax\Test\Fixture\TaxRule;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for CreateTaxRuleEntity
 *
 * Test Flow:
 * 1. Log in as default admin user.
 * 2. Go to Stores > Tax Rules.
 * 3. Click 'Add New Tax Rule' button.
 * 4. Fill in data according to dataSet
 * 5. Save Tax Rule.
 * 6. Perform all assertions.
 *
 * @group Tax_(CS)
 * @ZephyrId MAGETWO-20913
 */
class CreateTaxRuleEntityTest extends Injectable
{
    /**
     * @var TaxRuleIndex
     */
    protected $taxRuleIndexPage;

    /**
     * @var TaxRuleNew
     */
    protected $taxRuleNewPage;

    /**
     * @param TaxRuleIndex $taxRuleIndexPage
     * @param TaxRuleNew $taxRuleNewPage
     */
    public function __inject(
        TaxRuleIndex $taxRuleIndexPage,
        TaxRuleNew $taxRuleNewPage
    ) {
        $this->taxRuleIndexPage = $taxRuleIndexPage;
        $this->taxRuleNewPage = $taxRuleNewPage;
    }

    public function testCreateTaxRule(TaxRule $taxRule)
    {
        // Steps
        $this->taxRuleIndexPage->open();
        $this->taxRuleIndexPage->getGridPageActions()->addNew();
        $this->taxRuleNewPage->getTaxRuleForm()->fill($taxRule);
        $this->taxRuleNewPage->getFormPageActions()->save();
    }
}
