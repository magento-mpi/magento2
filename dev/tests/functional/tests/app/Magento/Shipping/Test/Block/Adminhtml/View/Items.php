<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Block\Adminhtml\View;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Items
 * Adminhtml shipping items on shipment view page
 */
class Items extends Grid
{
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
        if ($isSearchable) {
            $this->search($filter);
        }
        $location = '//div[@class="grid"]//tr[';
        $rowTemplate = 'td[contains(.,normalize-space("%s"))]';
        if ($isStrict) {
            $rowTemplate = 'td[.[normalize-space()="%s"]]';
        }
        $rows = [];
        foreach ($filter as $value) {
            $rows[] = sprintf($rowTemplate, $value);
        }
        $location = $location . implode(' and ', $rows) . ']';
        return $this->_rootElement->find($location, Locator::SELECTOR_XPATH);
    }
}
