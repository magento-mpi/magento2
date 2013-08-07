<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_CatalogRule_Helper_Data')->__('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Mage_CatalogRule_Helper_Data')->__('Conditions');
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
        $model = Mage::registry('current_promo_catalog_rule');

        //$form = new Magento_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Magento_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_catalog/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('Mage_CatalogRule_Helper_Data')->__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Conditions'),
            'title' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('Mage_Rule_Block_Conditions'));
/*
        $fieldset = $form->addFieldset('actions_fieldset', array('legend'=>Mage::helper('Mage_CatalogRule_Helper_Data')->__('Actions')));

        $fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Actions'),
            'title' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Actions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('Mage_Rule_Block_Actions'));

        $fieldset = $form->addFieldset('options_fieldset', array('legend'=>Mage::helper('Mage_CatalogRule_Helper_Data')->__('Options')));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Yes'),
                '0' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('No'),
            ),
        ));
*/
        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
