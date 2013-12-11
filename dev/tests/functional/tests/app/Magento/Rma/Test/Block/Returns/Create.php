<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Returns;

use Mtf\Fixture;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Return Item form block
 *
 * @package Magento\Rma\Test\Block\Returns
 */
class Create extends Form
{
    /**
     * Add Item to Return button selector
     *
     * @var string
     */
    protected $addItemToReturnButtonSelector = 'add-item-to-return';

    /**
     * Return button selector
     *
     * @var string
     */
    protected $returnButtonSelector = 'submit.save';

    /**
     * Fill form with custom fields
     *
     * @param $index
     * @param $returnItem
     */
    public function fillRma($index, $returnItem)
    {
        $products = $returnItem->getProductNames();

        $this->_rootElement
            ->find('items:item' . $index, Locator::SELECTOR_ID, 'select')
            ->setValue($products[0]);
        $this->_rootElement
            ->find('items:qty_requested' . $index, Locator::SELECTOR_ID)
            ->setValue($returnItem->getQuantity());
        $this->_rootElement
            ->find('items:resolution' . $index, Locator::SELECTOR_ID, 'select')
            ->setValue($returnItem->getResolution());
        $this->_rootElement
            ->find('items:condition' . $index, Locator::SELECTOR_ID, 'select')
            ->setValue($returnItem->getCondition());
        $this->_rootElement
            ->find('items:reason' . $index, Locator::SELECTOR_ID, 'select')
            ->setValue($returnItem->getReason());
    }

    /**
     * Submit add item to return
     */
    public function submitAddItemToReturn()
    {
        $this->_rootElement->find($this->addItemToReturnButtonSelector, Locator::SELECTOR_ID)->click();
    }

    /**
     * Submit return
     */
    public function submitReturn()
    {
        $this->_rootElement->find($this->returnButtonSelector, Locator::SELECTOR_ID)->click();
    }
}
