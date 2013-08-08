<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Main target rules properties edit form
 *
 * @category   Magento
 * @package    Magento_TargetRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main
    extends Magento_Adminhtml_Block_Widget_Form
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare Mail Target Rule Edit form
     *
     * @return Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        /* @var $model Magento_TargetRule_Model_Rule */
        $model = Mage::registry('current_target_rule');
        $form = new Magento_Data_Form();


        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('Magento_TargetRule_Helper_Data')->__('General Rule Information')
        ));

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('Magento_TargetRule_Helper_Data')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('Magento_TargetRule_Helper_Data')->__('Priority'),
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('Magento_TargetRule_Helper_Data')->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('Magento_TargetRule_Helper_Data')->__('Active'),
                '0' => Mage::helper('Magento_TargetRule_Helper_Data')->__('Inactive'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $fieldset->addField('apply_to', 'select', array(
            'label'     => Mage::helper('Magento_TargetRule_Helper_Data')->__('Apply To'),
            'name'      => 'apply_to',
            'required'  => true,
            'options'   => Mage::getSingleton('Magento_TargetRule_Model_Rule')->getAppliesToOptions(true),
        ));

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'         => 'from_date',
            'label'        => Mage::helper('Magento_TargetRule_Helper_Data')->__('From Date'),
            'image'        => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => Magento_Date::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'         => 'to_date',
            'label'        => Mage::helper('Magento_TargetRule_Helper_Data')->__('To Date'),
            'image'        => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => Magento_Date::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat
        ));

        $fieldset->addField('positions_limit', 'text', array(
            'name'  => 'positions_limit',
            'label' => Mage::helper('Magento_TargetRule_Helper_Data')->__('Result Limit'),
            'note'  => Mage::helper('Magento_TargetRule_Helper_Data')->__('Maximum number of products that can be matched by this Rule. Capped to 20.'),
        ));


        Mage::dispatchEvent('targetrule_edit_tab_main_after_prepare_form', array('model' => $model, 'form' => $form,
            'block' => $this));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Magento_TargetRule_Helper_Data')->__('Rule Information');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Magento_TargetRule_Helper_Data')->__('Rule Information');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
