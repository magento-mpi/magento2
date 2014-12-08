<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\TestStep;

use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventIndex;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventNew;
use Mtf\TestStep\TestStepInterface;

/**
 * Delete all Catalog Events on backend.
 */
class DeleteAllCatalogEventsStep implements TestStepInterface
{
    /**
     * Catalog Event Page
     *
     * @var CatalogEventNew
     */
    protected $catalogEventNew;

    /**
     * Event Page
     *
     * @var CatalogEventIndex
     */
    protected $catalogEventIndex;

    /**
     * @construct
     * @param CatalogEventNew $catalogEventNew
     * @param CatalogEventIndex $catalogEventIndex
     */
    public function __construct(
        CatalogEventNew $catalogEventNew,
        CatalogEventIndex $catalogEventIndex
    ) {
        $this->catalogEventNew = $catalogEventNew;
        $this->catalogEventIndex = $catalogEventIndex;
    }

    /**
     * Delete Catalog Event on backend.
     *
     * @return void
     */
    public function run()
    {
        $this->catalogEventIndex->open();
        while ($this->catalogEventIndex->getEventGrid()->isFirstRowVisible()) {
            $this->catalogEventIndex->getEventGrid()->openFirstRow();
            $this->catalogEventNew->getPageActions()->delete();
        }
    }
}
