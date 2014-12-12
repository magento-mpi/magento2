<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CatalogEvent\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Catalog Events Adminhtml Block
 *
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
