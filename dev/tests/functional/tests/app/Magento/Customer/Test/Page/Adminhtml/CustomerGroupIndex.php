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
 * Class CustomerGroupIndex
 */
class CustomerGroupIndex extends BackendPage
{
    const MCA = 'customer/group/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'customerGroupGrid' => [
            'class' => 'Magento\Customer\Test\Block\Adminhtml\Group\CustomerGroupGrid',
            'locator' => '#customerGroupGrid',
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
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Customer\Test\Block\Adminhtml\Group\CustomerGroupGrid
     */
    public function getCustomerGroupGrid()
    {
        return $this->getBlockInstance('customerGroupGrid');
    }
}
