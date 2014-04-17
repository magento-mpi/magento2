<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class CustomerIndexEdit
 *
 * @package Magento\Customer\Test\Page\Adminhtml
 */
class CustomerIndexEdit extends BackendPage
{
    const MCA = 'customer/index/edit';

    protected $_blocks = [
        'customerForm' => [
            'name' => 'customerForm',
            'class' => 'Magento\Customer\Test\Block\Adminhtml\Edit\CustomerForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Customer\Test\Block\Adminhtml\Edit\CustomerForm
     */
    public function getCustomerForm()
    {
        return $this->getBlockInstance('customerForm');
    }
}
