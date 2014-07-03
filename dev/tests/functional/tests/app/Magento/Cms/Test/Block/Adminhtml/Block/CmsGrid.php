<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Block;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class CmsGrid
 * Adminhtml Cms Block management grid
 */
class CmsGrid extends GridInterface
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => '#cmsBlockGrid_filter_title'
        ],
        'identifier' => [
            'selector' => '#cmsBlockGrid_filter_identifier',
        ],
        'store_id' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-store-filter-store-id"]',
            'input' => 'select',
        ],
        'is_active' => [
            'selector' => '#cmsBlockGrid_filter_is_active',
            'input' => 'select',
        ],
        'creation_time_from' => [
            'selector' => '[name="creation_time[from]"]',
        ],
        'update_time_from' => [
            'selector' => '[name="update_time[from]"]',
        ],
    ];

    /**
     * An element locator which allows to select first entity in grid
     *
     * @var string
     */
    protected $firstGrid = '#cmsBlockGrid_table tbody tr:first-child td';

    /**
     * Sort and open first entity in grid
     *
     * @throws \Exception
     */
    public function sortAndOpen()
    {
        $this->sortGridByField('creation_time', 'asc');
        $this->sortGridByField('creation_time');
        $rowItem = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_CSS);
        if ($rowItem->isVisible()) {
            $rowItem->find($this->firstGrid, Locator::SELECTOR_CSS)->click();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }
}
