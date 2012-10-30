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
 * Invitation Adminhtml Block
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 */

class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize invitation manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_customersegment';
        $this->_blockGroup = 'Enterprise_CustomerSegment';
        $this->_headerText = Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Manage Segments');
        $this->_addButtonLabel = Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Add Segment');
        parent::_construct();
    }

}
