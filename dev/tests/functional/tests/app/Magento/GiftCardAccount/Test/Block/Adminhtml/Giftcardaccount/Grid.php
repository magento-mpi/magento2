<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;
use Mtf\Client\Element\Locator;
use Mtf\Client\Element;

/**
 * Class Grid
 * Gift card account grid block
 */
class Grid extends AbstractGrid
{
    /**
     * Locator for edit link
     *
     * @var string
     */
    protected $editLink = '.col-code';

    /**
     *  Name for 'Sort' link
     *
     * @var string
     */
    protected $sortLinkName = 'entity_id';
    /**
     * Initialize block elements
     *
     * @var array
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
        ],
        'state' => [
            'selector' => '#giftcardaccountGrid_filter_state'
        ],
        'date_expires' => [
            'selector' => 'date_expires'
        ]
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
        $this->sortGridByField($this->sortLinkName);
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
    public function searchAndOpen(array $filter, $isSearchable = false)
    {
        $this->sortGridByField($this->sortLinkName);
        $selectItem = $this->getRow($filter, $isSearchable);
        if ($selectItem->isVisible()) {
            $selectItem->find($this->editLink)->click();
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
    public function getCode(array $filter, $isSearchable = false)
    {
        $this->sortGridByField($this->sortLinkName);
        $selectItem = $this->getRow($filter, $isSearchable);
        if ($selectItem->isVisible()) {
            return $selectItem->find($this->editLink)->getText();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }
}
