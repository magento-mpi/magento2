<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Rating_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_controller = 'rating';

        $this->_updateButton('save', 'label', Mage::helper('Magento_Rating_Helper_Data')->__('Save Rating'));
        $this->_updateButton('delete', 'label', Mage::helper('Magento_Rating_Helper_Data')->__('Delete Rating'));

        if( $this->getRequest()->getParam($this->_objectId) ) {

            $ratingData = Mage::getModel('Magento_Rating_Model_Rating')
                ->load($this->getRequest()->getParam($this->_objectId));

            Mage::register('rating_data', $ratingData);
        }


    }

    public function getHeaderText()
    {
        if( Mage::registry('rating_data') && Mage::registry('rating_data')->getId() ) {
            return Mage::helper('Magento_Rating_Helper_Data')->__("Edit Rating", $this->escapeHtml(Mage::registry('rating_data')->getRatingCode()));
        } else {
            return Mage::helper('Magento_Rating_Helper_Data')->__('New Rating');
        }
    }
}
