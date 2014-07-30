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
 * Class GiftRegistryAddSelect
 */
class GiftRegistryAddSelect extends FrontendPage
{
    const MCA = 'giftregistry/index/addselect';

    protected $_blocks = [
        'giftRegistryTypeBlock' => [
            'name' => 'giftRegistryTypeBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Edit',
            'locator' => '#form-validate',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Edit
     */
    public function getGiftRegistryTypeBlock()
    {
        return $this->getBlockInstance('giftRegistryTypeBlock');
    }
}
