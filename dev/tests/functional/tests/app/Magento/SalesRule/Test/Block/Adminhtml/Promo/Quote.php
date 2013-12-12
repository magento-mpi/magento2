<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Block\Adminhtml\Promo;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

class Quote extends Grid
{
    protected function _init()
    {
        parent::_init();
        $this->filters = array('name' => array('selector' => '#promo_quote_grid_filter_name'));
    }

    public function getIdOfRow($filter, $isSearchable = true)
    {
        $rid = -1;
        $this->search($filter, $isSearchable);
        $rowItem = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_CSS);
        if ($rowItem->isVisible()) {
            $idElement = $rowItem->find('td.col-rule_id');
            $rid = $idElement->getText();
        }
        return $rid;
    }
}
