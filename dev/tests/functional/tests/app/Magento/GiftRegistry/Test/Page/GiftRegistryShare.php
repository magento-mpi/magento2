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
 * Class GiftRegistryShare
 */
class GiftRegistryShare extends FrontendPage
{
    const MCA = 'giftregistry/index/share';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'giftRegistryShareForm' => [
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Share',
            'locator' => '#giftregistry-sharing-form',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Share
     */
    public function getGiftRegistryShareForm()
    {
        return $this->getBlockInstance('giftRegistryShareForm');
    }
}
