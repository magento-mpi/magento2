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
}
