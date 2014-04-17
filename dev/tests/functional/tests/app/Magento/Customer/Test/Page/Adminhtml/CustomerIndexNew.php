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
 * Class CustomerIndexNew
 *
 * @package Magento\Customer\Test\Page\Adminhtml
 */
class CustomerIndexNew extends BackendPage
{
    const MCA = 'customer/index/new';

    protected $_blocks = [
        'blockMessages' => [
            'name' => 'blockMessages',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'customerForm' => [
            'name' => 'customerForm',
            'class' => 'Magento\Customer\Test\Block\Adminhtml\Edit\CustomerForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getBlockMessages()
    {
        return $this->getBlockInstance('blockMessages');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Customer\Test\Block\Adminhtml\Edit\CustomerForm
     */
    public function getCustomerForm()
    {
        return $this->getBlockInstance('customerForm');
    }
}
