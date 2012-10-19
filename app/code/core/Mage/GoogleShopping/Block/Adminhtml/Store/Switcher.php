<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml GoogleShopping Store Switcher
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Block_Adminhtml_Store_Switcher extends Mage_Backend_Block_Store_Switcher
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
    public function __construct()
    {
        parent::__construct();
        $this->setUseConfirm(false)->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)));
    }
}
