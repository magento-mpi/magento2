<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog rule edit form block
 */

class Mage_Adminhtml_Block_Promo_Catalog_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Apply" button
     * Add "Save and Continue" button
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'promo_catalog';

        parent::_construct();

        $this->_addButton('save_apply', array(
            'class'   => 'save',
            'label'   => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Save and Apply'),
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'save',
                        'target' => '#edit_form',
                        'eventData' => array(
                            'action' => array(
                                'args' => array('auto_apply' => 1),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Save and Continue Edit'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 10);
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $rule = Mage::registry('current_promo_catalog_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('Mage_CatalogRule_Helper_Data')->__("Edit Rule '%1'", $this->escapeHtml($rule->getName()));
        }
        else {
            return Mage::helper('Mage_CatalogRule_Helper_Data')->__('New Rule');
        }
    }

}
