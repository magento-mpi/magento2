<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Event;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class EventGrid
 * Events grid of Catalog Events
 */
class Grid extends AbstractGrid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'category_name' => [
            'selector' => 'input[name="category"]'
        ],
        'start_on' => [
            'selector' => '[name="date_start[from]"]'
        ],
        'end_on' => [
            'selector' => '[name="date_end[from]"]'
        ],
        'status' => [
            'selector' => 'select[name="status"]',
            'input' => 'select'
        ],
        'countdown_ticker' => [
            'selector' => 'select[name="display_state"]',
            'input' => 'select'
        ],
        'sort_order' => [
            'selector' => 'input[name="sort_order"]'
        ],
    ];

    /**
     * Check if specific row exists in grid
     *
     * @param array $filter
     * @param bool $isSearchable
     * @return bool
     */
    public function isRowVisible(array $filter, $isSearchable = false)
    {
        $this->search(['category_name' => $filter['category_name']]);
        return $this->getRow($filter, $isSearchable)->isVisible();
    }

    protected function getRow(array $filter, $isSearchable = true)
    {
        if ($isSearchable) {
            $this->search($filter);
        }
        $location = '//div[@class="grid"]//tr[';
        $rows = array();
        foreach ($filter as $value) {
            if (strripos($value, 'PM') || strripos($value, 'AM')) {
                $value = substr($value, 0, strlen($value) - 11);
            }
            $rows[] = 'td[contains(text(),normalize-space("' . $value . '"))]';
        }
        $location = $location . implode(' and ', $rows) . ']';
        return $this->_rootElement->find($location, Locator::SELECTOR_XPATH);
    }
}
