<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Registry Adminhtml Block
 */
class Magento_GiftRegistry_Block_Adminhtml_Giftregistry extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize gift registry manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_giftregistry';
        $this->_blockGroup = 'Magento_GiftRegistry';
        $this->_headerText = __('Gift Registry Types');
        $this->_addButtonLabel = __('Add Gift Registry Type');
        parent::_construct();
    }
}
