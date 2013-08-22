<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml GoogleShopping Store Switcher
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Block_Adminhtml_Store_Switcher extends Magento_Backend_Block_Store_Switcher
{
    /**
     * Whether the switcher should show default option
     *
     * @var bool
     */
    protected $_hasDefaultOption = false;

    /**
     * Set overriden params
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseConfirm(false)->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)));
    }
}
