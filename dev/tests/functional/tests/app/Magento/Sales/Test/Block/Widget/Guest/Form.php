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
class Form extends \Mtf\Block\Form
{
    protected $_mapping = [
        'order_id' => [
            'selector' => '#oar-order-id'
        ],
        'billing_last_name' => [
            'selector' => '#oar-billing-lastname'
        ],
        'find_order_by' => [
            'selector' => '#quick-search-type-id',
            'input' => 'select'
        ],
        'email_address' => [
            'selector' => '#oar_email'
        ],
    ];

    /**
     * Search button selector
     *
     * @var string
     */
    protected $searchButtonSelector = '.action.submit';

    /**
     * Submit search form
     */
    public function submit()
    {
        $this->_rootElement->find($this->searchButtonSelector, Locator::SELECTOR_CSS)->click();
    }
}
