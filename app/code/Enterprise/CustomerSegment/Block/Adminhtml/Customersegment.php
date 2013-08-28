<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segment Adminhtml Block
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 */

class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * Initialize customer segment manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_customersegment';
        $this->_blockGroup = 'Enterprise_CustomerSegment';
        $this->_headerText = __('Segments');
        $this->_addButtonLabel = __('Add Segment');
        parent::_construct();
    }

}
