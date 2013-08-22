<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segment Adminhtml Block
 *
 * @category   Magento
 * @package    Magento_CustomerSegment
 */

class Magento_CustomerSegment_Block_Adminhtml_Customersegment extends Magento_Backend_Block_Widget_Grid_Container
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
