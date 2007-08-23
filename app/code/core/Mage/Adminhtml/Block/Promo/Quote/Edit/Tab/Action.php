<?php
/**
 * description
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  Promo_Quote
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Action extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'POST'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>__('General Information')));

        $fieldset->addField('simple_action', 'select', array(
            'label'     => __('Apply'),
            'name'      => 'simple_action',
            'options'    => array(
                'by_percent' => __('Percent of product price discount'),
                'by_fixed' => __('Fixed amount discount'),
            ),
        ));

        $fieldset->addField('discount_amount', 'text', array(
            'name' => 'discount_amount',
            'required' => true,
            'label' => __('Discount amount'),
        ));

        $fieldset->addField('discount_qty', 'text', array(
            'name' => 'discount_qty',
            'label' => __('Maximum Qty Discount is Applied to'),
        ));

        $fieldset->addField('simple_free_shipping', 'select', array(
            'label'     => __('Free ground shipping'),
            'title'     => __('Free ground shipping'),
            'name'      => 'simple_free_shipping',
            'options'    => array(
                0 => __('No'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM => __('For matching items only'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS => __('For the shipping address'),
            ),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => __('Stop further rules processing'),
            'title'     => __('Stop further rules processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
        ));

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}