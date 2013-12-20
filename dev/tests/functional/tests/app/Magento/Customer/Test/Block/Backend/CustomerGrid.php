<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Backend;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class CustomerGrid
 * Backend customer grid
 *
 * @package Magento\Customer\Test\Block\Backend
 */
class CustomerGrid extends Grid
{
    /**
     * 'Add New' customer button
     *
     * @var string
     */
    protected $addNewCustomer = "../*[@class='page-actions']//*[@id='add']";
    
    /**
     * {@inheritDoc}
     */
    protected $filters = array(
        'email' => array(
            'selector' => '#customerGrid_filter_email'
        ),
        'group' => array(
            'selector' => '#customerGrid_filter_group',
            'input' => 'select'
        ),
    );

    /**
     * Customer group selector by customer email
     *
     * @var string
     */
    protected $customerGroupSelector = '//tr[td[text()[normalize-space()="%s"]]]/td[normalize-space(@class)="col-group"]';

    /**
     * {@inheritDoc}
     */
    protected $waitForSelector = 'div#customerGrid';

    /**
     * {@inheritDoc}
     */
    protected $waitForSelectorVisible = false;

    /**
     * Add new customer
     */
    public function addNewCustomer()
    {
        $this->_rootElement->find($this->addNewCustomer, Locator::SELECTOR_XPATH)->click();
    }
}
