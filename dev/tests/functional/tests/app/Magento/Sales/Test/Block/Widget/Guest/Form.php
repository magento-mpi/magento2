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

namespace Magento\Sales\Test\Block\Widget\Guest;

use Mtf\Fixture;
use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Orders and Returns form search block
 *
 * @package Magento\Sales\Test\Block\Widget\Guest
 */
class Form extends Block
{
    /**
     * Search button selector
     *
     * @var string
     */
    protected $searchButtonSelector = '.action.submit';

    /**
     * Order Id selector
     *
     * @var string
     */
    protected $orderIdSelector = 'oar-order-id';

    /**
     * Order Billing Lastname selector
     *
     * @var string
     */
    protected $orderBillingLastnameSelector = 'oar-billing-lastname';

    /**
     * Order Email selector
     *
     * @var string
     */
    protected $orderEmailSelector = 'oar_email';

    /**
     * Order Search Type selector
     *
     * @var string
     */
    protected $orderSearchTypeSelector = 'quick-search-type-id';

    /**
     * Fill form with custom fields
     *
     * @param OrderSearch $orderSearch
     */
    public function fillCustom($orderSearch)
    {
        $this->_rootElement->find($this->orderIdSelector, Locator::SELECTOR_ID)->setValue($orderSearch->getOrderId());
        $this->_rootElement->find($this->orderBillingLastnameSelector, Locator::SELECTOR_ID)->setValue($orderSearch->getBillingLastname());
        $this->_rootElement->find($this->orderEmailSelector, Locator::SELECTOR_ID)->setValue($orderSearch->getEmailAddress());
        $this->_rootElement->find($this->orderSearchTypeSelector, Locator::SELECTOR_ID, 'select')->setValue($orderSearch->getFindOrderBy());
    }

    /**
     * Submit search form
     */
    public function submit()
    {
        $this->_rootElement->find($this->searchButtonSelector, Locator::SELECTOR_CSS)->click();
    }
}
