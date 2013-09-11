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
 * Product attribute edit page
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Attribute;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{

    protected function _construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'catalog_product_attribute';

        parent::_construct();

        if($this->getRequest()->getParam('popup')) {
            $this->_removeButton('back');
            if ($this->getRequest()->getParam('product_tab') != 'variations') {
                $this->_addButton(
                    'save_in_new_set',
                    array(
                        'label'     => __('Save in New Attribute Set'),
                        'class'     => 'save',
                        'onclick'   => 'saveAttributeInNewSet(\''
                            . __('Enter Name for New Attribute Set')
                            . '\')',
                    )
                );
            }
        } else {
            $this->_addButton(
                'save_and_edit_button',
                array(
                    'label'     => __('Save and Continue Edit'),
                    'class'     => 'save',
                    'data_attribute'  => array(
                        'mage-init' => array(
                            'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                        ),
                    ),
                ),
                100
            );
        }

        $this->_updateButton('save', 'label', __('Save Attribute'));
        $this->_updateButton('save', 'class', 'save primary');
        $this->_updateButton('save', 'data_attribute', array(
            'mage-init' => array(
                'button' => array('event' => 'save', 'target' => '#edit_form'),
            ),
        ));

        if (!\Mage::registry('entity_attribute') || !\Mage::registry('entity_attribute')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', __('Delete Attribute'));
        }
    }

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (\Mage::registry('entity_attribute')->getId()) {
            $frontendLabel = \Mage::registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return __('Edit Product Attribute "%1"', $this->escapeHtml($frontendLabel));
        }
        return __('New Product Attribute');
    }

    /**
     * Retrieve URL for validation
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    /**
     * Retrieve URL for save
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            '*/'.$this->_controller.'/save',
            array(
                '_current' => true,
                'back' => null,
                'product_tab' => $this->getRequest()->getParam('product_tab')
            )
        );
    }
}
