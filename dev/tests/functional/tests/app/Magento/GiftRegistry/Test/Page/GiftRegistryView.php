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
 * Class GiftRegistryView
 */
class GiftRegistryView extends FrontendPage
{
    const MCA = 'giftregistry/view/index';

    protected $_blocks = [
        'giftRegistryItemsBlock' => [
            'name' => 'giftRegistryItemsBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Items',
            'locator' => '.giftregistry.items',
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
