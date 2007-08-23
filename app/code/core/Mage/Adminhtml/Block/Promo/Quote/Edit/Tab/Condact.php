<?php
/**
 * description
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  Promo_Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Condact extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('promo/form.phtml');
        $this->setNewConditionChildUrl($this->getUrl('*/promo_quote/newConditionHtml'));
        $this->setNewActionChildUrl($this->getUrl('*/promo_quote/newActionHtml'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'POST'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('conditions_fieldset', array('legend'=>__('Conditions')));
		/*
    	$fieldset->addField('use_conditions', 'checkbox', array(
            'name' => 'use_conditions',
            'label' => __('Use advanced conditions'),
        ));
        */
    	$fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
        ))->setRule($model)->setRenderer(Mage::getHelper('rule/conditions'));
        /*
        $fieldset = $form->addFieldset('actions_fieldset', array('legend'=>__('Actions')));

    	$fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => __('Actions'),
            'title' => __('Actions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getHelper('rule/actions'));
        */

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}