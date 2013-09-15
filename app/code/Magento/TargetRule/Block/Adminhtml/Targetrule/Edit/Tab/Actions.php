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
 * TargetRule Adminhtml Edit Tab Actions Block
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
namespace Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab;

class Actions
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface

{
    /**
     * Prepare target rule actions form before rendering HTML
     *
     * @return \Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Actions
     */
    protected function _prepareForm()
    {
        /* @var $model Magento_TargetRule_Model_Rule */
        $model  = $this->_coreRegistry->registry('current_target_rule');
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset   = $form->addFieldset('actions_fieldset', array(
            'legend' => __('Product Result Conditions (leave blank for matching all products)'))
        );
        $newCondUrl = $this->getUrl('*/targetrule/newActionsHtml/', array(
            'form'  => $fieldset->getHtmlId()
        ));
        $renderer   = \Mage::getBlockSingleton('Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset')
            ->setTemplate('Magento_TargetRule::edit/conditions/fieldset.phtml')
            ->setNewChildUrl($newCondUrl);
        $fieldset->setRenderer($renderer);

        $element    = $fieldset->addField('actions', 'text', array(
            'name'      => 'actions',
            'required'  => true
        ));
        $element->setRule($model);
        $element->setRenderer(\Mage::getBlockSingleton('Magento\TargetRule\Block\Adminhtml\Actions\Conditions'));

        $model->getActions()->setJsFormObject($fieldset->getHtmlId());
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
        return __('Products to Display');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Products to Display');
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
