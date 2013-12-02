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

namespace Magento\Rma\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class OrdersAndReturnsSearch
 * Manage orders and returns page
 *
 * @package Magento\Rma\Test\Page
 */
class OrdersAndReturnsSearch extends Page
{
    /**
     * URL for orders and returns search page
     */
    const MCA = 'sales/guest/form';

    /**
     * Orders and Returns search form
     *
     * @var \Magento\Rma\Test\Block\Form\OrdersAndReturnsSearch
     */
    protected $ordersAndReturnsForm;

    /**
     * Form wrapper selector
     *
     * @var string
     */
    private $formWrapperSelector = '.form.orders.search';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->ordersAndReturnsForm = Factory::getBlockFactory()->getMagentoRmaFormOrdersAndReturnsSearch(
            $this->_browser->find($this->formWrapperSelector, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get search block form
     *
     * @return \Magento\Rma\Test\Block\Form\OrdersAndReturnsSearch
     */
    public function getOrdersAndReturnsSearchForm()
    {
        return $this->ordersAndReturnsForm;
    }
}