<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;
use Mtf\Client\Element;

/**
 * Class GiftCardAccountIndexGrid
 *
 * @package Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount
 */
class GiftCardAccountIndexGrid extends Grid
{
    /**
     * Initialize block elements
     *
     * @var array $filters
     */
    protected $filters = [
        'code' => [
            'selector' => '#giftcardaccountGrid_filter_code'
        ],
        'balanceFrom' => [
            'selector' => '#giftcardaccountGrid_filter_balance_from'
        ],
        'balanceTo' => [
            'selector' => '#giftcardaccountGrid_filter_balance_to'
        ]
    ];

    /**
     * Obtain specific row in grid
     *
     * @param array $filter
     * @param bool $isSearchable
     * @return Element
     */
    protected function getRow(array $filter, $isSearchable = true)
    {
        if ($isSearchable) {
            $this->search($filter);
        }
        $location = '//div[@class="grid"]//tbody/tr[1][';
        $rows = array();
        foreach ($filter as $value) {
            $rows[] = 'td[contains(.,"' . $value . '")]';
        }
        $location = $location . implode(' and ', $rows) . ']';
        return $this->_rootElement->find($location, Locator::SELECTOR_XPATH);
    }

    /**
     * Search for item and select it
     *
     * @param array $filter
     * @param bool $isSearchable
     * @throws \Exception
     */
    public function searchAndSelect(array $filter, $isSearchable = false)
    {
        $selectItem = $this->getRow($filter, $isSearchable);
        if ($selectItem->isVisible()) {
            $selectItem->find('.col-code')->click();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }

    /**
     * Search for item and select it
     *
     * @param array $filter
     * @param bool $isSearchable
     * @return array|string
     * @throws \Exception
     */
    public function searchCode(array $filter, $isSearchable = false)
    {
        $selectItem = $this->getRow($filter, $isSearchable);
        if ($selectItem->isVisible()) {
            return $selectItem->find('.col-code')->getText();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }
}
