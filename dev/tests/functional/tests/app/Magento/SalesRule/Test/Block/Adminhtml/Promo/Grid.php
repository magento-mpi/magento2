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
 * Backend sales rule grid.
 */
class Grid extends AbstractGrid
{
    /**
     * Id of a row selector.
     *
     * @var string
     */
    protected $rowIdSelector = 'td.col-rule_id';
    
    /**
     * Locator for promo quote form.
     *
     * @var string
     */
    protected $promoQuoteFormSelector = 'div#promo_catalog_edit_tabs';
    
    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => 'input[name="name"]',
        ]
    ];

    /**
     * Locator value for link in sales rule name column.
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-name]';

    /**
     * An element locator which allows to select entities in grid.
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-name';

    /**
     * Return the id of the row that matched the search filter.
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
}
