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
        'blockMessages' => [
            'name' => 'blockMessages',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'customerGrid' => [
            'name' => 'customerGrid',
            'class' => 'Magento\Customer\Test\Block\Adminhtml\Customer\Grid',
            'locator' => '#customerGrid',
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
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Customer\Test\Block\Adminhtml\Customer\Grid
     */
    public function getCustomerGrid()
    {
        return $this->getBlockInstance('customerGrid');
    }
}
