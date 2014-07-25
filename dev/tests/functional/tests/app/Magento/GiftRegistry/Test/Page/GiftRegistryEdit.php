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

    protected $_blocks = [
        'generalInformationForm' => [
            'name' => 'generalInformationForm',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Edit\Registry',
            'locator' => '//fieldset[div/div/input[@name="title"]]',
            'strategy' => 'xpath',
        ],
        'actionsToolbarBlock' => [
            'name' => 'actionsToolbarBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Edit\ActionsToolbar',
            'locator' => '//div[div/button[@id="submit.save"]]',
            'strategy' => 'xpath',
        ],
        'eventInformationForm' => [
            'name' => 'eventInformationForm',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Edit\Registry',
            'locator' => '//fieldset[div/div/select[@name="event_country"]]',
            'strategy' => 'xpath',
        ],
        'recipientsInformationForm' => [
            'name' => 'recipientsInformationForm',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Edit\Registrants',
            'locator' => '.recipients',
            'strategy' => 'css selector',
        ],
        'shippingAddressForm' => [
            'name' => 'shippingAddressForm',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Address\Edit',
            'locator' => '.shipping_address',
            'strategy' => 'css selector',
        ],
        'giftRegistryPropertiesForm' => [
            'name' => 'giftRegistryPropertiesForm',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Edit\Registry',
            'locator' => '//fieldset[div/div/select[@id="baby_gender"]]',
            'strategy' => 'xpath',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Edit\Registry
     */
    public function getGeneralInformationForm()
    {
        return $this->getBlockInstance('generalInformationForm');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Edit\ActionsToolbar
     */
    public function getActionsToolbarBlock()
    {
        return $this->getBlockInstance('actionsToolbarBlock');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Edit\Registry
     */
    public function getEventInformationForm()
    {
        return $this->getBlockInstance('eventInformationForm');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Edit\Registrants
     */
    public function getRecipientsInformationForm()
    {
        return $this->getBlockInstance('recipientsInformationForm');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Address\Edit
     */
    public function getShippingAddressForm()
    {
        return $this->getBlockInstance('shippingAddressForm');
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Edit\Registry
     */
    public function getGiftRegistryPropertiesForm()
    {
        return $this->getBlockInstance('giftRegistryPropertiesForm');
    }
}
