<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class GiftRegistryCustomerEdit
 * Gift registry page on backend
 */
class GiftRegistryCustomerEdit extends BackendPage
{
    const MCA = 'giftregistry_customer/edit';

    protected $_blocks = [
        'actionsToolbarBlock' => [
            'name' => 'actionsToolbarBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Adminhtml\Edit\ActionsToolbar',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'sharingInfoBlock' => [
            'name' => 'sharingInfoBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit\Sharing',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Adminhtml\Edit\ActionsToolbar
     */
    public function getActionsToolbarBlock()
    {
        return $this->getBlockInstance('actionsToolbarBlock');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit\Sharing
     */
    public function getSharingInfoBlock()
    {
        return $this->getBlockInstance('sharingInfoBlock');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
