<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Catalog Events Adminhtml Block
 *
 * @category   Magento
 * @package    Magento_CatalogEvent
 */
class Event extends Container
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_event';
        $this->_blockGroup = 'Magento_CatalogEvent';
        $this->_headerText = __('Events');
        $this->_addButtonLabel = __('Add Catalog Event');
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-catalogevent';
    }
}
