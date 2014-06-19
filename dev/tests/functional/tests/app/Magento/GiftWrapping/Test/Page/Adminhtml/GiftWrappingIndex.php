<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class GiftWrappingIndex
 */
class GiftWrappingIndex extends BackendPage
{
    const MCA = 'admin/giftwrapping/index';

    protected $_blocks = [
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'giftWrappingGrid' => [
            'name' => 'giftWrappingGrid',
            'class' => 'Magento\GiftWrapping\Test\Block\Adminhtml\Grid',
            'locator' => '#giftwrappingGrid',
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
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\GiftWrapping\Test\Block\Adminhtml\Grid
     */
    public function getGiftWrappingGrid()
    {
        return $this->getBlockInstance('giftWrappingGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
