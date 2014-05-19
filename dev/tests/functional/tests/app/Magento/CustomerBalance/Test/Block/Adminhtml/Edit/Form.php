<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Adminhtml\Edit;

use Magento\Customer\Test\Block\Adminhtml\Edit\Form as Tabs;

/**
 * Class Form
 * Form for creation of the customer
 *
 */
class Form extends Tabs
{
    /**
     * Getting customer tab object
     *
     * @return \Magento\CustomerBalance\Test\Block\Adminhtml\Edit\Tab
     */
    public function getCustomerTab()
    {
        return $this->getTabElement('store_credit');
    }
}
