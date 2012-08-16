<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Enterprise_GiftRegistry';
        $this->_controller = 'adminhtml_giftregistry';

        parent::_construct();

        if (Mage::registry('current_giftregistry_type')) {
            $this->_updateButton('save', 'label', Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Save'));
            $this->_updateButton('save', 'onclick', 'editForm.submit(\'' . $this->getSaveUrl() . '\');');

            $confirmMessage = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__("Deleting this gift registry type will also remove all customers' gift registries created based on it. Are you sure you want to proceed?");
            $this->_updateButton('delete', 'label', Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Delete'));
            $this->_updateButton('delete', 'onclick',
                'deleteConfirm(\'' . $this->jsQuoteEscape($confirmMessage) . '\', \'' . $this->getDeleteUrl() . '\')'
            );

            $this->_addButton('save_and_continue_edit', array(
                'class'   => 'save',
                'label'   => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Save and Continue Edit'),
                'onclick' => 'editForm.submit(\'' . $this->getSaveUrl() . '\' + \'back/edit/\')',
            ), 3);
        }
    }

    /**
     * Return form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $type = Mage::registry('current_giftregistry_type');
        if ($type->getId()) {
            return Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__("Edit '%s' Gift Registry Type", $this->escapeHtml($type->getLabel()));
        }
        else {
            return Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('New Gift Registry Type');
        }
    }

    /**
     * Return save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        $type = Mage::registry('current_giftregistry_type');
        return $this->getUrl('*/*/save', array('id' => $type->getId(), 'store' => $type->getStoreId()));
    }
}
