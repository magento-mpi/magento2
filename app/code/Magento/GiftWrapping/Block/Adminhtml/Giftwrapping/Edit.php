<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Block\Adminhtml\Giftwrapping;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize form
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_giftwrapping';
        $this->_blockGroup = 'Magento_GiftWrapping';

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

        $giftWrapping = $this->_coreRegistry->registry('current_giftwrapping_model');
        if ($giftWrapping && $giftWrapping->getId()) {
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
        $wrapping = $this->_coreRegistry->registry('current_giftwrapping_model');
        if ($wrapping->getId()) {
            $title = $this->escapeHtml($wrapping->getDesign());
            return __('Edit Gift Wrapping "%1"', $title);
        } else {
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
        $wrapping = $this->_coreRegistry->registry('current_giftwrapping_model');

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
        $wrapping = $this->_coreRegistry->registry('current_giftwrapping_model');
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
