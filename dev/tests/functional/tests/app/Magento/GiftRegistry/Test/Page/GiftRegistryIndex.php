<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class GiftRegistryIndex
 */
class GiftRegistryIndex extends FrontendPage
{
    const MCA = 'giftregistry/index';

    protected $_blocks = [
        'actionsToolbar' => [
            'name' => 'actionsToolbar',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\ActionsToolbar',
            'locator' => '.actions-toolbar',
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
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Grid',
            'locator' => '#giftregistry-table',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\ActionsToolbar
     */
    public function getActionsToolbar()
    {
        return $this->getBlockInstance('actionsToolbar');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Grid
     */
    public function getGiftRegistryGrid()
    {
        return $this->getBlockInstance('giftRegistryGrid');
    }
}
