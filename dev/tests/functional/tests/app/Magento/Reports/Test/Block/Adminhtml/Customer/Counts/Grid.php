<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Customer\Counts;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Order count report grid
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Preceding sibling
     *
     * @var string
     */
    protected $preceding = 'tr[contains(@class,"pointer") and *[contains(@class,"col-period") and contains(.,"%s")]]';

    /**
     * Following sibling
     *
     * @var string
     */
    protected $following = 'tr[contains(@class,"pointer")]';

    /**
     * Current pointer
     *
     * @var string
     */
    protected $date = 'td[contains(@class,"col-period") and contains(.,"%s")]';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'customer' => [
            'selector' => 'td[contains(@class,"col-name") and normalize-space(.)="%s"]',
        ],
        'orders' => [
            'selector' => 'td[contains(@class,"col-orders_count") and normalize-space(.)="%s"]',
        ],
        'average' => [
            'selector' => 'td[contains(@class,"col-average") and contains(.,"%s")]',
        ],
        'total' => [
            'selector' => 'td[contains(@class,"col-total") and contains(.,"%s")]',
        ],
    ];

    /**
     * Obtain specific row in grid
     *
     * @param array $filter
     * @param bool $isSearchable
     * @param bool $isStrict
     * @return Element
     */
    protected function getRow(array $filter, $isSearchable = true, $isStrict = true)
    {
        $this->date = sprintf($this->date, $filter['date']);
        $location = '//div[@class="grid"]//tr[((preceding-sibling::' . sprintf($this->preceding, $filter['date'])
            . ' and following-sibling::' . $this->following . ') or ' . $this->date . ') and ';
        unset($filter['date']);
        $rows = [];
        foreach ($filter as $key => $value) {
            $rows[] = sprintf($this->filters[$key]['selector'], $value);
        }
        $location = $location . implode(' and ', $rows) . ']';
        if (!$this->_rootElement->find($location, Locator::SELECTOR_XPATH)->isVisible()) {
            $location = str_replace($this->following, 'tr[last()]', $location);
            $location = str_replace($this->date, $this->date . ' or last()', $location);
        }
        return $this->_rootElement->find($location, Locator::SELECTOR_XPATH);
    }
}
