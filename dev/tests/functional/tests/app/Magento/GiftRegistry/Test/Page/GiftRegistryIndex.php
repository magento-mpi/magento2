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
        'listCustomerBlock' => [
            'name' => 'listCustomerBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\ListCustomer',
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
            'class' => 'Magento\GiftRegistry\Test\Block\Grid',
            'locator' => '#giftregistry-table',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\ListCustomer
     */
    public function getListCustomerBlock()
    {
        return $this->getBlockInstance('listCustomerBlock');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Grid
     */
    public function getGiftRegistryGrid()
    {
        return $this->getBlockInstance('giftRegistryGrid');
    }
}
