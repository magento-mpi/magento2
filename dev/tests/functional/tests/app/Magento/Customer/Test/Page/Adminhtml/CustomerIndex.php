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
 * Class CustomerIndex
 *
 * @package Magento\Customer\Test\Page\Adminhtml
 */
class CustomerIndex extends BackendPage
{
    const MCA = 'customer/index';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'pageActionsBlock' => [
            'name' => 'pageActionsBlock',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'customerGridBlock' => [
            'name' => 'customerGridBlock',
            'class' => 'Magento\Customer\Test\Block\Adminhtml\CustomerGrid',
            'locator' => '#customerGrid',
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
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }

    /**
     * @return \Magento\Customer\Test\Block\Adminhtml\CustomerGrid
     */
    public function getCustomerGridBlock()
    {
        return $this->getBlockInstance('customerGridBlock');
    }
}
