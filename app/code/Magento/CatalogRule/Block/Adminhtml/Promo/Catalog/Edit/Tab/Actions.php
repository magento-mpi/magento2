<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Block\Adminhtml\Promo\Catalog\Edit\Tab;

use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Actions extends Generic implements TabInterface
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
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_catalog_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset(
            'action_fieldset',
            array('legend' => __('Update Prices Using the Following Information'))
        );

        $fieldset->addField(
            'simple_action',
            'select',
            array(
                'label' => __('Apply'),
                'name' => 'simple_action',
                'options' => array(
                    'by_percent' => __('By Percentage of the Original Price'),
                    'by_fixed' => __('By Fixed Amount'),
                    'to_percent' => __('To Percentage of the Original Price'),
                    'to_fixed' => __('To Fixed Amount')
                )
            )
        );

        $fieldset->addField(
            'discount_amount',
            'text',
            array(
                'name' => 'discount_amount',
                'required' => true,
                'class' => 'validate-not-negative-number',
                'label' => __('Discount Amount')
            )
        );

        $fieldset->addField(
            'sub_is_enable',
            'select',
            array(
                'name' => 'sub_is_enable',
                'label' => __('Enable Discount to Subproducts'),
                'title' => __('Enable Discount to Subproducts'),
                'onchange' => 'hideShowSubproductOptions(this);',
                'values' => array(0 => __('No'), 1 => __('Yes'))
            )
        );

        $fieldset->addField(
            'sub_simple_action',
            'select',
            array(
                'label' => __('Apply'),
                'name' => 'sub_simple_action',
                'options' => array(
                    'by_percent' => __('By Percentage of the Original Price'),
                    'by_fixed' => __('By Fixed Amount'),
                    'to_percent' => __('To Percentage of the Original Price'),
                    'to_fixed' => __('To Fixed Amount')
                )
            )
        );

        $fieldset->addField(
            'sub_discount_amount',
            'text',
            array(
                'name' => 'sub_discount_amount',
                'required' => true,
                'class' => 'validate-not-negative-number',
                'label' => __('Discount Amount')
            )
        );

        $fieldset->addField(
            'stop_rules_processing',
            'select',
            array(
                'label' => __('Stop Further Rules Processing'),
                'title' => __('Stop Further Rules Processing'),
                'name' => 'stop_rules_processing',
                'options' => array('1' => __('Yes'), '0' => __('No'))
            )
        );

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
