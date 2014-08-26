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
 * Class GiftRegistryItems
 */
class GiftRegistryItems extends FrontendPage
{
    const MCA = 'giftregistry/index/items';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'giftRegistryItemsBlock' => [
            'class' => 'Magento\GiftRegistry\Test\Block\Items',
            'locator' => '#shopping-cart-table',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Items
     */
    public function getGiftRegistryItemsBlock()
    {
        return $this->getBlockInstance('giftRegistryItemsBlock');
    }
}
