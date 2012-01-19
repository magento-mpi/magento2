<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Adminhtml report filter form for coupons report
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon extends Mage_Sales_Block_Adminhtml_Report_Filter_Form
{
    /**
     * Prepare form
     *
     * @return Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {

            $fieldset->addField('price_rule_type', 'select', array(
                'name'    => 'price_rule_type',
                'options' => array(
                    Mage::helper('Mage_Reports_Helper_Data')->__('Any'),
                    Mage::helper('Mage_Reports_Helper_Data')->__('Specified')
                ),
                'label'   => Mage::helper('Mage_Reports_Helper_Data')->__('Shopping Cart Price Rule'),
            ));

            $rulesList = Mage::getResourceModel('Mage_SalesRule_Model_Resource_Report_Rule')->getUniqRulesNamesList();

            $rulesListOptions = array();

            foreach ($rulesList as $key => $ruleName) {
                $rulesListOptions[] = array(
                    'label' => $ruleName,
                    'value' => $key,
                    'title' => $ruleName
                );
            }

            $fieldset->addField('rules_list', 'multiselect', array(
                'name'      => 'rules_list',
                'values'    => $rulesListOptions,
                'display'   => 'none'
            ), 'price_rule_type');

            $this->setChild('form_after', $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Form_Element_Dependence')
                ->addFieldMap($htmlIdPrefix . 'price_rule_type', 'price_rule_type')
                ->addFieldMap($htmlIdPrefix . 'rules_list', 'rules_list')
                ->addFieldDependence('rules_list', 'price_rule_type', '1')
            );
        }

        return $this;
    }
}
