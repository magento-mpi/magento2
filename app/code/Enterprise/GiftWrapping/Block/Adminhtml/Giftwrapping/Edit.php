<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
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
            'label'   => __('Save and Continue Edit'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 3);

        if (Mage::registry('current_giftwrapping_model') && Mage::registry('current_giftwrapping_model')->getId()) {
            $confirmMessage = __('Are you sure you want to delete this gift wrapping?');
            $this->_updateButton('delete', 'onclick',
                'deleteConfirm(\'' . $this->jsQuoteEscape($confirmMessage) . '\', \'' . $this->getDeleteUrl() . '\')'
            );
        }

        $this->_formScripts[] = "
                // Temporary solution will be replaced after refactoring Gift Wrapping functionality
                function uploadImagesForPreview() {
                    var fform = jQuery('#edit_form');
                    fform.find('input, select, textarea').each(function() {
                        jQuery(this).attr('type') === 'file' ?
                            jQuery(this).addClass('required-entry') :
                            jQuery(this).addClass('ignore-validate temp-ignore-validate');
                    });
                    fform.on('invalid-form.validate', function() {
                        fform.find('.temp-ignore-validate').removeClass('ignore-validate temp-ignore-validate');
                        fform.find('[type=\"file\"]').removeClass('required-entry');
                        fform.off('invalid-form.validate');
                    });
                    fform.triggerHandler('save', [{action: '" . $this->getUploadUrl()  . "'}]);
                }
            ";
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
            return __('Edit Gift Wrapping "%1"', $title);
        }
        else {
            return __('New Gift Wrapping');
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
