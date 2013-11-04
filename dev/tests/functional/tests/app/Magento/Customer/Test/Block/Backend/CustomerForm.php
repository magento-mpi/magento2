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
     * Custom tab classes for customer form
     *
     * @var array
     */
    protected $_tabClasses = array(
        'customer_info_tabs_account' => '\\Magento\\Backend\\Test\\Block\\Customer\\Edit\\Tab\\Account'
    );
}
