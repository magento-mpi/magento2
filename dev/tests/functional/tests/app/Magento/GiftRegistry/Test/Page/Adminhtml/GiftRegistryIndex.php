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
 * Class GiftRegistryIndex
 * GiftRegistryIndex page
 */
class GiftRegistryIndex extends BackendPage
{
    const MCA = 'admin/giftregistry';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages',
            'strategy' => 'css selector',
        ],
        'giftRegistryGrid' => [
            'name' => 'giftRegistryGrid',
            'class' => 'Magento\GiftRegistry\Test\Block\Adminhtml\Grid',
            'locator' => '[data-grid-id="giftregistryGrid"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Adminhtml\Grid
     */
    public function getGiftRegistryGrid()
    {
        return $this->getBlockInstance('giftRegistryGrid');
    }
}
