<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Block\Adminhtml\Promo;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Backend sales rule grid
 */
class Grid extends AbstractGrid
{
    /**
     * Id of a row selector
     *
     * @var string
     */
    protected $rowIdSelector = 'td.col-rule_id';
    
    /**
     * @var string
     */
    protected $promoQuoteFormSelector = 'div#promo_catalog_edit_tabs';

    /**
     * First row selector
     *
     * @var string
     */
    protected $firstRowSelector = '//tr[1]/td[@data-column="name"]';
    
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'rule_id' => [
            'selector' => '#promo_quote_grid_filter_rule_id'
        ],
        'name' => [
            'selector' => '#promo_quote_grid_filter_name',
        ]
    ];

    /**
     * Locator value for link in sales rule name column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-name]';

    /**
     * Return the id of the row that matched the search filter
     *
     * @param $filter
     * @param bool $isSearchable
     * @return array|int|string
     */
    public function getIdOfRow($filter, $isSearchable = true)
    {
        $rid = -1;
        $this->search($filter, $isSearchable);
        $rowItem = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_CSS);
        if ($rowItem->isVisible()) {
            $idElement = $rowItem->find($this->rowIdSelector);
            $rid = $idElement->getText();
        }
        return $rid;
    }

    /**
     * Check whether first row is visible
     *
     * @return bool
     */
    public function isFirstRowVisible()
    {
        return $this->_rootElement->find($this->firstRowSelector, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Open first item in grid
     *
     * @return void
     */
    public function openFirstRow()
    {
        $this->_rootElement->find($this->firstRowSelector, Locator::SELECTOR_XPATH)->click();
    }
}
