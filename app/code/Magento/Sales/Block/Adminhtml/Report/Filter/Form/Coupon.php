<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Adminhtml report filter form for coupons report
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Adminhtml_Report_Filter_Form_Coupon extends Magento_Sales_Block_Adminhtml_Report_Filter_Form
{
    /**
     * Flag that keep info should we render specific dependent element or not
     *
     * @var bool
     */
    protected $_renderDependentElement = false;

    /**
     * @var Magento_SalesRule_Model_Resource_Report_RuleFactory
     */
    protected $_reportRule;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Sales_Model_Order_ConfigFactory $orderConfig
     * @param Magento_SalesRule_Model_Resource_Report_RuleFactory $reportRule
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Sales_Model_Order_ConfigFactory $orderConfig,
        Magento_SalesRule_Model_Resource_Report_RuleFactory $reportRule,
        array $data = array()
    ) {
        $this->_reportRule = $reportRule;
        parent::__construct($registry, $formFactory, $coreData, $context, $orderConfig, $data);
    }

    /**
     * Prepare form
     *
     * @return Magento_Sales_Block_Adminhtml_Report_Filter_Form_Coupon
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var Magento_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Magento_Data_Form_Element_Fieldset) {

            $fieldset->addField('price_rule_type', 'select', array(
                'name'    => 'price_rule_type',
                'options' => array(
                    __('Any'),
                    __('Specified')
                ),
                'label'   => __('Shopping Cart Price Rule'),
            ));

            $rulesList = $this->_reportRule->create()->getUniqRulesNamesList();

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

            $this->_renderDependentElement = true;
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
        if ($this->_renderDependentElement) {
            $form = $this->getForm();
            $htmlIdPrefix = $form->getHtmlIdPrefix();

            /**
             * Form template has possibility to render child block 'form_after', but we can't use it because parent
             * form creates appropriate child block and uses this alias. In this case we can't use the same alias
             * without core logic changes, that's why the code below was moved inside method '_afterToHtml'.
             */
            /** @var $formAfterBlock Magento_Adminhtml_Block_Widget_Form_Element_Dependence */
            $formAfterBlock = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Form_Element_Dependence',
                'adminhtml.block.widget.form.element.dependence'
            );
            $formAfterBlock->addFieldMap($htmlIdPrefix . 'price_rule_type', 'price_rule_type')
                ->addFieldMap($htmlIdPrefix . 'rules_list', 'rules_list')
                ->addFieldDependence('rules_list', 'price_rule_type', '1');
            $html = $html . $formAfterBlock->toHtml();
        }

        return $html;
    }
}
