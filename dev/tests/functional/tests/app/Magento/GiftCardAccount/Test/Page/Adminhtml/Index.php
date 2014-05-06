<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class Index
 *
 * @package Magento\GiftCardAccount\Test\Page\Adminhtml
 */
class Index extends BackendPage
{
    const MCA = 'admin/giftcardaccount/index';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'giftCardAccount' => [
            'name' => 'giftCardAccount',
            'class' => 'Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount\Grid',
            'locator' => '#giftcardaccountGrid',
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
     * @return \Magento\GiftCardAccount\Test\Block\Adminhtml\Giftcardaccount\Grid
     */
    public function getGiftCardAccount()
    {
        return $this->getBlockInstance('giftCardAccount');
    }
}
