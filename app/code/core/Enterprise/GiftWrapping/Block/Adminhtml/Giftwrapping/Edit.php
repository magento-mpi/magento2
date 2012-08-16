<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_giftwrapping';
        $this->_blockGroup = 'Enterprise_GiftWrapping';

        parent::_construct();

        $this->_removeButton('reset');

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit(\'' . $this->getSaveUrl() . '\' + \'back/edit/\')',
        ), 3);

        if (Mage::registry('current_giftwrapping_model') && Mage::registry('current_giftwrapping_model')->getId()) {
            $confirmMessage = Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Are you sure you want to delete this gift wrapping?');
            $this->_updateButton('delete', 'onclick',
                'deleteConfirm(\'' . $this->jsQuoteEscape($confirmMessage) . '\', \'' . $this->getDeleteUrl() . '\')'
            );
        }

        $this->_formScripts[] = '
                function uploadImagesForPreview() {
                    var fform = $(editForm.formId)
                    fform.getElements().each(function(elm){
                        if (Element.readAttribute(elm, "type") == "file") {
                            Element.addClassName(elm, "required-entry")
                        } else {
                            Element.addClassName(elm, "ignore-validate")
                        }
                    });

                    editForm.submit("' . $this->getUploadUrl()  . '");
                }
            ';
    }

    /**
     * Return form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $wrapping = Mage::registry('current_giftwrapping_model');
        if ($wrapping->getId()) {
            $title = $this->escapeHtml($wrapping->getDesign());
            return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Edit Gift Wrapping "%s"', $title);
        }
        else {
            return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('New Gift Wrapping');
        }
    }

    /**
     * Return save url (used for Save and Continue button)
     *
     * @return string
     */
    public function getSaveUrl()
    {
        $wrapping = Mage::registry('current_giftwrapping_model');

        if ($wrapping) {
            $url = $this->getUrl('*/*/save', array('id' => $wrapping->getId(), 'store' => $wrapping->getStoreId()));
        } else {
            $url = $this->getUrl('*/*/save');
        }
        return $url;
    }

    /**
     * Return upload url (used for Upload button)
     *
     * @return string
     */
    public function getUploadUrl()
    {
        $wrapping = Mage::registry('current_giftwrapping_model');
        $params = array();
        if ($wrapping) {
            $params['store'] = $wrapping->getStoreId();
            if ($wrapping->getId()) {
                $params['id'] = $wrapping->getId();
            }
        }

        return $this->getUrl('*/*/upload', $params);
    }

}
