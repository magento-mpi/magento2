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
 */
class CustomerIndexNew extends BackendPage
{
    const MCA = 'customer/index/new';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'pageActionsBlock' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'customerForm' => [
            'class' => 'Magento\Customer\Test\Block\Adminhtml\Edit\CustomerForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }

    /**
     * @return \Magento\Customer\Test\Block\Adminhtml\Edit\CustomerForm
     */
    public function getCustomerForm()
    {
        return $this->getBlockInstance('customerForm');
    }
}
