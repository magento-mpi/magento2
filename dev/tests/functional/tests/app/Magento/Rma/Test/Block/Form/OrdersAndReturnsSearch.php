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

use Mtf\Fixture;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Orders and Returns form search block
 *
 * @package Magento\Rma\Test\Block\Form
 */
class OrdersAndReturnsSearch extends Form
{
    /**
     * Search button selector
     *
     * @var string
     */
    private $searchButtonSelector = '.action.submit';

    /**
     * Fill form with custom fields
     *
     * @param $order
     * @param $findOrderBy
     */
    public function fillCustom($order, $findOrderBy)
    {
        $this->_rootElement->find('#oar-order-id', Locator::SELECTOR_CSS)->setValue($order->getOrderId());
        $this->_rootElement->find('#oar-billing-lastname', Locator::SELECTOR_CSS)->setValue($order->getBillingAddress()->getLastName());
        //$this->_rootElement->find('#quick-search-type-id', Locator::SELECTOR_CSS)->setValue($findOrderBy);
        $this->_rootElement->find('#oar_email', Locator::SELECTOR_CSS)->setValue($order->getCustomer()->getEmail());
    }

    /**
     * Submit search form
     */
    public function submit()
    {
        $this->_rootElement->find($this->searchButtonSelector, Locator::SELECTOR_CSS)->click();
    }
}
