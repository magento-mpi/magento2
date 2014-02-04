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

namespace Magento\CatalogEvent\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Event extends Container
{
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
    public function getHeaderCssClass() {
        return 'icon-head head-catalogevent';
    }
}
