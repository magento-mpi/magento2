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
 */
class CustomerIndexEdit extends BackendPage
{
    const MCA = 'customer/index/edit';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'titleBlock' => [
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page-title .title',
            'strategy' => 'css selector',
        ],
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
        'customerBalanceForm' => [
            'class' => 'Magento\CustomerBalance\Test\Block\Adminhtml\Edit\CustomerForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'balanceHistoryGrid' => [
            'class' => 'Magento\CustomerBalance\Test\Block\Adminhtml\Customer\Edit\Tab\Balance\History\Grid',
            'locator' => '#historyGrid_table',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->getBlockInstance('titleBlock');
    }

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

    /**
     * @return \Magento\CustomerBalance\Test\Block\Adminhtml\Edit\CustomerForm
     */
    public function getCustomerBalanceForm()
    {
        return $this->getBlockInstance('customerBalanceForm');
    }

    /**
     * @return \Magento\CustomerBalance\Test\Block\Adminhtml\Customer\Edit\Tab\Balance\History\Grid
     */
    public function getBalanceHistoryGrid()
    {
        return $this->getBlockInstance('balanceHistoryGrid');
    }
}
