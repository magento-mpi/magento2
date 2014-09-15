<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleNew;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;

/**
 * Class CatalogRuleEntityTest
 * Parent class for CatalogRule tests
 */
abstract class CatalogRuleEntityTest extends Injectable
{
    /**
     * Page CatalogRuleIndex
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleIndex;

    /**
     * Page CatalogRuleNew
     *
     * @var CatalogRuleNew
     */
    protected $catalogRuleNew;

    /**
     * Page AdminCache
     *
     * @var AdminCache
     */
    protected $adminCache;

    /**
     * Fixture CatalogRule
     *
     * @var array
     */
    protected $catalogRules = [];

    /**
     * Injection data
     *
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleNew $catalogRuleNew
     * @param AdminCache $adminCache
     * @return void
     */
    public function __inject(
        CatalogRuleIndex $catalogRuleIndex,
        CatalogRuleNew $catalogRuleNew,
        AdminCache $adminCache
    ) {
        $this->catalogRuleIndex = $catalogRuleIndex;
        $this->catalogRuleNew = $catalogRuleNew;
        $this->adminCache = $adminCache;
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        foreach ($this->catalogRules as $catalogRule) {
            $filter = ['name' => $catalogRule->getName()];
            $this->catalogRuleIndex->open();
            $this->catalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filter);
            $this->catalogRuleNew->getFormPageActions()->delete();
        }
        $this->catalogRules = [];
    }
}
