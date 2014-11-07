<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleNew;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;

/**
 * Class DeleteAllCatalogRulesStep
 * Delete all Catalog Rules on backend
 */
class DeleteAllCatalogRulesStep implements TestStepInterface
{
    /**
     * Catalog rule index page
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleIndex;

    /**
     * Catalog rule new and edit page
     *
     * @var CatalogRuleNew
     */
    protected $catalogRuleNew;

    /**
     * @construct
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleNew $catalogRuleNew
     */
    public function __construct(
        CatalogRuleIndex $catalogRuleIndex,
        CatalogRuleNew $catalogRuleNew
    ) {
        $this->catalogRuleIndex = $catalogRuleIndex;
        $this->catalogRuleNew = $catalogRuleNew;
    }

    /**
     * Delete Catalog Rule on backend
     *
     * @return array
     */
    public function run()
    {
        $this->catalogRuleIndex->open();
        while ($this->catalogRuleIndex->getCatalogRuleGrid()->isFirstRowVisible()) {
            $this->catalogRuleIndex->getCatalogRuleGrid()->openFirstRow();
            $this->catalogRuleNew->getFormPageActions()->delete();
        }
    }
}
