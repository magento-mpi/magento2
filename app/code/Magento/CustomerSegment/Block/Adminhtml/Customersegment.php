<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Customer Segment Adminhtml Block
 *
 */
namespace Magento\CustomerSegment\Block\Adminhtml;

class Customersegment extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize customer segment manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_customersegment';
        $this->_blockGroup = 'Magento_CustomerSegment';
        $this->_headerText = __('Segments');
        $this->_addButtonLabel = __('Add Segment');
        parent::_construct();
    }
}
