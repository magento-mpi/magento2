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
 * Poll edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Poll_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll';

        $this->_updateButton('save', 'label', Mage::helper('Mage_Poll_Helper_Data')->__('Save Poll'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Poll_Helper_Data')->__('Delete Poll'));

        $this->setValidationUrl($this->getUrl('*/*/validate', array('id' => $this->getRequest()->getParam($this->_objectId))));
    }

    public function getHeaderText()
    {
        if( Mage::registry('poll_data') && Mage::registry('poll_data')->getId() ) {
            return Mage::helper('Mage_Poll_Helper_Data')->__("Edit Poll '%s'", $this->escapeHtml(Mage::registry('poll_data')->getPollTitle()));
        } else {
            return Mage::helper('Mage_Poll_Helper_Data')->__('New Poll');
        }
    }
}
