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
 * Adminhtml tax rule Edit Container
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Tax_Rule_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'rule';
        $this->_controller = 'tax_rule';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Rule'));
        $this->_updateButton('delete', 'label', __('Delete Rule'));

        $this->_addButton('save_and_continue', array(
            'label'     => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 10);
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('tax_rule')->getId()) {
            return __("Edit Rule");
        }
        else {
            return __('New Rule');
        }
    }
}
