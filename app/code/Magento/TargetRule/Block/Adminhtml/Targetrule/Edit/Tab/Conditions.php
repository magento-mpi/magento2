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
 * TargetRule Adminhtml Edit Tab Conditions Block
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
class Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Conditions
    extends Magento_Backend_Block_Widget_Form_Generic
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Prepare target rule actions form before rendering HTML
     *
     * @return Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Conditions
     */
    protected function _prepareForm()
    {
        /* @var $model Magento_TargetRule_Model_Rule */
        $model  = $this->_coreRegistry->registry('current_target_rule');

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset   = $form->addFieldset('conditions_fieldset', array(
            'legend' => __('Product Match Conditions (leave blank for matching all products)'))
        );
        $newCondUrl = $this->getUrl('*/targetrule/newConditionHtml/', array(
            'form'  => $fieldset->getHtmlId()
        ));
        $renderer   = Mage::getBlockSingleton('Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset')
            ->setTemplate('Magento_TargetRule::edit/conditions/fieldset.phtml')
            ->setNewChildUrl($newCondUrl);
        $fieldset->setRenderer($renderer);

        $element    = $fieldset->addField('conditions', 'text', array(
            'name'      => 'conditions',
            'required'  => true,
        ));

        $element->setRule($model);
        $element->setRenderer(Mage::getBlockSingleton('Magento_TargetRule_Block_Adminhtml_Rule_Conditions'));

        $model->getConditions()->setJsFormObject($fieldset->getHtmlId());
        $form->setValues($model->getData());

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
        return __('Products to Match');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Products to Match');
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
