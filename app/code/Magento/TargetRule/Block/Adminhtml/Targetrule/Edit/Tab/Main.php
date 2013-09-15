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
namespace Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab;

class Main
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare Mail Target Rule Edit form
     *
     * @return \Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Main
     */
    protected function _prepareForm()
    {
        /* @var $model Magento_TargetRule_Model_Rule */
        $model = $this->_coreRegistry->registry('current_target_rule');
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();


        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('General Rule Information')
        ));

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => __('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => __('Priority'),
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => __('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array(
                '1' => __('Active'),
                '0' => __('Inactive'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $fieldset->addField('apply_to', 'select', array(
            'label'     => __('Apply To'),
            'name'      => 'apply_to',
            'required'  => true,
            'options'   => \Mage::getSingleton('Magento\TargetRule\Model\Rule')->getAppliesToOptions(true),
        ));

        $dateFormat = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'         => 'from_date',
            'label'        => __('From Date'),
            'image'        => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => \Magento\Date::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'         => 'to_date',
            'label'        => __('To Date'),
            'image'        => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => \Magento\Date::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat
        ));

        $fieldset->addField('positions_limit', 'text', array(
            'name'  => 'positions_limit',
            'label' => __('Result Limit'),
            'note'  => __('Maximum number of products that can be matched by this Rule. Capped to 20.'),
        ));

        $this->_eventManager->dispatch('targetrule_edit_tab_main_after_prepare_form', array(
            'model' => $model,
            'form' => $form,
            'block' => $this,
        ));

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
        return __('Rule Information');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Rule Information');
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
