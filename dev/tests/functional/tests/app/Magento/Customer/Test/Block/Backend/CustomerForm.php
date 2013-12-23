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

namespace Magento\Customer\Test\Block\Backend;

use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class CustomerForm
 * Form for creation of the customer
 *
 * @package Magento\Customer\Test\Block\Backend
 */
class CustomerForm extends FormTabs
{
    /**
     * {@inheritDoc}
     */
    protected $tabClasses = array(
        'customer_info_tabs_account' => '\Magento\Customer\Test\Block\Adminhtml\Edit\Tab\Account'
    );

    /**
     * {@inheritDoc}
     */
    protected $waitForSelector = 'div#customer_info_tabs';

    /**
     * {@inheritDoc}
     */
    protected $waitForSelectorVisible = false;
}
