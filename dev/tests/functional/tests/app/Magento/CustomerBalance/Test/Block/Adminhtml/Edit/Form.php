<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Adminhtml\Edit;

use Magento\Customer\Test\Block\Adminhtml\Edit\Form as ParentForm;

/**
 * Class Form
 * Form for creation store credits
 */
class Form extends ParentForm
{
    /**
     * Getting store credit tab object
     *
     * @return \Magento\CustomerBalance\Test\Block\Adminhtml\Customer\Edit\Tab\Tab
     */
    public function getStoreCreditTab()
    {
        return $this->getTabElement('store_credit');
    }
}
