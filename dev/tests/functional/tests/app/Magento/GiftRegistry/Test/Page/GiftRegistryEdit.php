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
 * Class GiftRegistryEdit
 */
class GiftRegistryEdit extends FrontendPage
{
    const MCA = 'giftregistry/index/edit';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'actionsToolbarBlock' => [
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Edit\ActionsToolbar',
            'locator' => '//div[div/button[@id="submit.save"]]',
            'strategy' => 'xpath',
        ],
        'customerEditForm' => [
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Edit',
            'locator' => '.form-giftregistry-edit',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Edit\ActionsToolbar
     */
    public function getActionsToolbarBlock()
    {
        return $this->getBlockInstance('actionsToolbarBlock');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Edit
     */
    public function getCustomerEditForm()
    {
        return $this->getBlockInstance('customerEditForm');
    }
}
