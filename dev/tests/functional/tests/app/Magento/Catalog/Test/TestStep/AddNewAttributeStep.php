<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;
use Mtf\TestStep\TestStepInterface;

/**
 * Add New Attribute.
 */
class AddNewAttributeStep implements TestStepInterface
{
    /**
     * Catalog Product Attribute Index page.
     *
     * @var CatalogProductAttributeIndex
     */
    protected $catalogProductAttributeIndex;

    /**
     * @constructor
     * @param CatalogProductAttributeIndex $catalogProductAttributeIndex
     */
    public function __construct(CatalogProductAttributeIndex $catalogProductAttributeIndex)
    {
        $this->catalogProductAttributeIndex = $catalogProductAttributeIndex;
    }

    /**
     * Add New Attribute Set Step.
     *
     * @return void
     */
    public function run()
    {
        $this->catalogProductAttributeIndex->getPageActionsBlock()->addNew();
    }
}
