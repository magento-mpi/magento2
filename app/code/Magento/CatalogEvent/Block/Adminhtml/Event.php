<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Events Adminhtml Block
 *
 * @category   Magento
 * @package    Magento_CatalogEvent
 */

class Magento_CatalogEvent_Block_Adminhtml_Event extends Magento_Backend_Block_Widget_Grid_Container
{
    public function _construct()
    {
        $this->_controller = 'adminhtml_event';
        $this->_blockGroup = 'Magento_CatalogEvent';
        $this->_headerText = __('Events');
        $this->_addButtonLabel = __('Add Catalog Event');
        parent::_construct();
    }

    public function getHeaderCssClass() {
        return 'icon-head head-catalogevent';
    }
}
