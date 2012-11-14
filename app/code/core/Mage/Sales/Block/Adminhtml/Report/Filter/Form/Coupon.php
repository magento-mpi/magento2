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
     * Flag that keep info should we renderer specific dependence element or not
     *
     * @var bool
     */
    protected $_renderDependenceElement = false;

    /**
     * Prepare form
     *
     * @return Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

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

            $this->_renderDependenceElement = true;
        }

        return $this;
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        /**
         * Form template has possibility to render child block 'form_after', but we can not use this because parent form
         * already create appropriate child block and used this alias. In this case we can not use same alias without
         * changing of core logic, that's why bottom code placed inside method '_afterToHtml'.
         */
        /** @var $formAfterBlock Mage_Adminhtml_Block_Widget_Form_Element_Dependence */
        $formAfterBlock = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Form_Element_Dependence',
            'adminhtml.block.widget.form.element.dependence'
        );
        $formAfterBlock->addFieldMap($htmlIdPrefix . 'price_rule_type', 'price_rule_type')
            ->addFieldMap($htmlIdPrefix . 'rules_list', 'rules_list')
            ->addFieldDependence('rules_list', 'price_rule_type', '1');

        return $html . $formAfterBlock->toHtml();
    }
}
