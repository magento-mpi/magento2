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
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Promo_Catalog_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'promo_catalog';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_CatalogRule_Helper_Data')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_CatalogRule_Helper_Data')->__('Delete Rule'));

        $rule = Mage::registry('current_promo_catalog_rule');

        if (!$rule || !$rule->isDeleteable()) {
            $this->_removeButton('delete');
        }

        if (!$rule || !$rule->isReadonly()) {
            $this->_addButton('save_apply', array(
                'class'=>'save',
                'label'=>Mage::helper('Mage_CatalogRule_Helper_Data')->__('Save and Apply'),
                'onclick'=>"$('rule_auto_apply').value=1; editForm.submit()",
            ));
            $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 10);
            $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit/') } ";
        } else {
            $this->_removeButton('reset');
            $this->_removeButton('save');
        }
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('current_promo_catalog_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('Mage_CatalogRule_Helper_Data')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }
        else {
            return Mage::helper('Mage_CatalogRule_Helper_Data')->__('New Rule');
        }
    }

}
