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
 * 1.*Preconditions:*
 * 2.TaxRate is created
 * 3.
 * 4.*Steps:*
 * 5. Log in as default admin user.
 * 6. Go to Stores > Tax Rules.
 * 7. Click 'Add New Tax Rule' button.
 * 8. Fill in data according to dataSet
 * 9. Save Tax Rule.
 * 10. Perform all assertions.
 *
 * @group Tax_(CS)
 * @ZephyrId MTA-3
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
