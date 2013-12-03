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

namespace Magento\Rma\Test\Block\Form;

use Magento\Sales\Test\Fixture\PaypalExpressOrder;
use Mtf\Fixture;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Orders and Returns form search block
 *
 * @package Magento\Rma\Test\Block\Form
 */
class ReturnItem extends Form
{
    /**
     * Add Item to Return button selector
     *
     * @var string
     */
    private $addItemToReturnButtonSelector = 'add-item-to-return';

    /**
     * Return button selector
     *
     * @var string
     */
    private $returnButtonSelector = 'submit.save';

    /**
     * Fill form with custom fields
     *
     */
    public function fillCustom($index)
    {
        // TODO:  Get data from repository.
        $this->_rootElement->find('#row' . $index, Locator::SELECTOR_ID)->setValue('1');
        $this->_rootElement->find('#items:qty_requested' . $index, Locator::SELECTOR_ID)->setValue('1');
        $this->_rootElement->find('#items:resolution' . $index, Locator::SELECTOR_ID)->setValue('4');
        $this->_rootElement->find('#items:condition' . $index, Locator::SELECTOR_ID)->setValue('7');
        $this->_rootElement->find('#items:reason' . $index, Locator::SELECTOR_ID)->setValue('10');
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
