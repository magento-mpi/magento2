<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductAttributeNew;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Mtf\TestStep\TestStepInterface;

/**
 * Save attributeSet on attribute set page.
 */
class SaveAttributeSetStep implements TestStepInterface
{
    /**
     * Catalog ProductSet Edit page.
     *
     * @var CatalogProductSetEdit
     */
    protected $catalogProductSetEdit;

    /**
     * @constructor
     * @param CatalogProductSetEdit $catalogProductSetEdit
     */
    public function __construct(CatalogProductSetEdit $catalogProductSetEdit)
    {
        $this->catalogProductSetEdit = $catalogProductSetEdit;
    }

    /**
     * Save attributeSet on attribute set page.
     *
     * @return void
     */
    public function run()
    {
        $this->catalogProductSetEdit->getPageActions()->save();
    }
}
