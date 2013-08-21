<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Tag Store Switcher
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Store_Switcher extends Magento_Backend_Block_Store_Switcher
{
    /**
     * @var bool
     */
    protected $_hasDefaultOption = false;

    /**
     * Set overriden params
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseConfirm(false)->setSwitchUrl(
            $this->getUrl('*/*/*/', array('store' => null, '_current' => true))
        );
    }
}
