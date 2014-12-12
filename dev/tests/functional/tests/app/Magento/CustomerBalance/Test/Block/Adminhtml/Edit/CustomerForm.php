<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerBalance\Test\Block\Adminhtml\Edit;

use Magento\Customer\Test\Block\Adminhtml\Edit\CustomerForm as ParentForm;

/**
 * Class CustomerForm
 * Form for creation store credits
 */
class CustomerForm extends ParentForm
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
