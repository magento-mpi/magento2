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
namespace Magento\Sales\Block\Adminhtml\Report\Filter\Form;

class Coupon extends \Magento\Sales\Block\Adminhtml\Report\Filter\Form
{
    /**
     * Flag that keep info should we render specific dependent element or not
     *
     * @var bool
     */
    protected $_renderDependentElement = false;

    /**
     * Prepare form
     *
     * @return \Magento\Sales\Block\Adminhtml\Report\Filter\Form\Coupon
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var \Magento\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof \Magento\Data\Form\Element\Fieldset) {

            $fieldset->addField('price_rule_type', 'select', array(
                'name'    => 'price_rule_type',
                'options' => array(
                    __('Any'),
                    __('Specified')
                ),
                'label'   => __('Shopping Cart Price Rule'),
            ));

            $rulesList = \Mage::getResourceModel('Magento\SalesRule\Model\Resource\Report\Rule')->getUniqRulesNamesList();

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
            /** @var $formAfterBlock \Magento\Adminhtml\Block\Widget\Form\Element\Dependence */
            $formAfterBlock = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Form\Element\Dependence',
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
