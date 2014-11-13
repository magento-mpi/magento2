<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;
use Mtf\TestStep\TestStepInterface;

/**
 * Save attribute on attribute page.
 */
class SaveAttributeStep implements TestStepInterface
{
    /**
     * Catalog product attribute edit page.
     *
     * @var CatalogProductAttributeNew
     */
    protected $attributeNew;

    /**
     * @constructor
     * @param CatalogProductAttributeNew $attributeNew
     */
    public function __construct(CatalogProductAttributeNew $attributeNew)
    {
        $this->attributeNew = $attributeNew;
    }

    /**
     * Fill custom attribute form on attribute page.
     *
     * @return void
     */
    public function run()
    {
        $this->attributeNew->getPageActions()->save();
    }
}
