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
 *
 * @package Magento\GiftRegistry\Test\Page
 */
class GiftRegistryEdit extends FrontendPage
{
    const MCA = 'giftregistry/index/edit';

    protected $_blocks = [
        'actionsToolbarBlock' => [
            'name' => 'actionsToolbarBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Edit\ActionsToolbar',
            'locator' => '//div[div/button[@id="submit.save"]]',
            'strategy' => 'xpath',
        ],
        'shippingAddressForm' => [
            'name' => 'shippingAddressForm',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Address\Edit',
            'locator' => '.shipping_address',
            'strategy' => 'css selector',
        ],
        'customerEditForm' => [
            'name' => 'customerEditForm',
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
     * @return \Magento\GiftRegistry\Test\Block\Customer\Address\Edit
     */
    public function getShippingAddressForm()
    {
        return $this->getBlockInstance('shippingAddressForm');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Edit
     */
    public function getCustomerEditForm()
    {
        return $this->getBlockInstance('customerEditForm');
    }
}
