<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Class CartItems
 * Backend cart items gift registry grid
 */
class CartItems extends Grid
{
    /**
     * Grid row selector
     *
     * @var string
     */
    protected $rowSelector = '//tr';

    /**
     * Grid cell selector
     *
     * @var string
     */
    protected $cellSelector = '[td[contains(.,"%s")]]';

    /**
     * Search for item and select it
     *
     * @param array $filter
     * @throws \Exception
     * @return void
     */
    public function searchAndSelect(array $filter)
    {
        foreach ($filter as $item) {
            $this->rowSelector .= sprintf($this->cellSelector, $item);
        }
        $selectItem = $this->_rootElement->find($this->rowSelector, Locator::SELECTOR_XPATH)->find($this->selectItem);
        if ($selectItem->isVisible()) {
            $selectItem->click();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }

    /**
     * Perform selected massaction over checked items
     *
     * @param array $items
     * @param array|string $action [array -> key = value from first select; value => value from subselect]
     * @param bool $acceptAlert [optional]
     * @param string $massActionSelection [optional]
     * @return void
     */
    public function massaction(array $items, $action, $acceptAlert = false, $massActionSelection = '')
    {
        parent::massaction($items, $action, $acceptAlert, $massActionSelection);
        $this->_rootElement->acceptAlert();
    }
}
