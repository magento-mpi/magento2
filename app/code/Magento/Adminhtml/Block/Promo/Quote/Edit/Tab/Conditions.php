<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * description
 *
 * @category    Magento
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Promo_Quote_Edit_Tab_Conditions
    extends Magento_Adminhtml_Block_Widget_Form
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Conditions');
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
        $model = Mage::registry('current_promo_quote_rule');

        //$form = new Magento_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Magento_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_quote/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>__('Apply the rule only if the following conditions are met (leave blank for all products).')
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('Magento_Rule_Block_Conditions'));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
