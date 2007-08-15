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
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'POST'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        if ($model->getId()) {
        	$fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }
        
    	$fieldset->addField('product_ids', 'hidden', array(
            'name' => 'product_ids',
        ));
        
    	$fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => __('Rule Name'),
            'title' => __('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
            'style' => 'width: 98%; height: 100px;',
            'required' => true,
        ));

        $fieldset->addField('coupon_code', 'text', array(
            'name' => 'coupon_code',
            'label' => __('Coupon code'),
        ));
        
    	$fieldset->addField('is_active', 'select', array(
            'label'     => __('Status'),
            'title'     => __('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => __('Enabled'),
                '0' => __('Disabled'),
            ),
        ));
        
        $stores = Mage::getResourceModel('core/store_collection')
            ->addFieldToFilter('store_id', array('neq'=>0))
            ->load()->toOptionArray();
            
    	$fieldset->addField('store_ids', 'multiselect', array(
            'name'      => 'store_ids[]',
            'label'     => __('Stores'),
            'title'     => __('Stores'),
            'required'  => true,
            'values'    => $stores,
        ));
        
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load()->toOptionArray();

    	$fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => __('Customer Groups'),
            'title'     => __('Customer Groups'),
            'required'  => true,
            'values'    => $customerGroups,
        ));
        
    	$fieldset->addField('from_date', 'date', array(
            'name' => 'from_date',
            'label' => __('From Date'),
            'title' => __('From Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
        ));
        
    	$fieldset->addField('to_date', 'date', array(
            'name' => 'to_date',
            'label' => __('To Date'),
            'title' => __('To Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
        ));
    	
        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => __('Stop further rules processing'),
            'title'     => __('Stop further rules processing'),
            'name'      => 'stop_rules_processing',
            'required' => true,
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