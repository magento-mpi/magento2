<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Promo_Catalog_Edit_Tab_Actions
    extends Magento_Backend_Block_Widget_Form_Generic
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_catalog_rule');

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array(
                'legend' => __('Update Prices Using the Following Information')
            )
        );

        $fieldset->addField('simple_action', 'select', array(
            'label'     => __('Apply'),
            'name'      => 'simple_action',
            'options'   => array(
                'by_percent'    => __('By Percentage of the Original Price'),
                'by_fixed'      => __('By Fixed Amount'),
                'to_percent'    => __('To Percentage of the Original Price'),
                'to_fixed'      => __('To Fixed Amount'),
            ),
        ));

        $fieldset->addField('discount_amount', 'text', array(
            'name'      => 'discount_amount',
            'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => __('Discount Amount'),
        ));

        $fieldset->addField('sub_is_enable', 'select', array(
            'name'      => 'sub_is_enable',
            'label'     => __('Enable Discount to Subproducts'),
            'title'     => __('Enable Discount to Subproducts'),
            'onchange'  => 'hideShowSubproductOptions(this);',
            'values'    => array(
                0 => __('No'),
                1 => __('Yes')
            )
        ));

        $fieldset->addField('sub_simple_action', 'select', array(
            'label'     => __('Apply'),
            'name'      => 'sub_simple_action',
            'options'   => array(
                'by_percent'    => __('By Percentage of the Original Price'),
                'by_fixed'      => __('By Fixed Amount'),
                'to_percent'    => __('To Percentage of the Original Price'),
                'to_fixed'      => __('To Fixed Amount'),
            ),
        ));

        $fieldset->addField('sub_discount_amount', 'text', array(
            'name'      => 'sub_discount_amount',
            'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => __('Discount Amount'),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => __('Stop Further Rules Processing'),
            'title'     => __('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'   => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
        ));

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
