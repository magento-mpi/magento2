<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Block_Adminhtml_Process_Edit_Tab_Main
    extends Magento_Adminhtml_Block_Widget_Form
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_index_process');
        $form = new Magento_Data_Form();
        $form->setHtmlIdPrefix('index_process_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend'=>Mage::helper('Mage_Index_Helper_Data')->__('General'), 'class'=>'fieldset-wide')
        );

        $fieldset->addField('process_id', 'hidden', array('name' => 'process', 'value'=>$model->getId()));

        $fieldset->addField('name', 'note', array(
            'label' => Mage::helper('Mage_Index_Helper_Data')->__('Index Name'),
            'title' => Mage::helper('Mage_Index_Helper_Data')->__('Index Name'),
            'text'  => '<strong>'.$model->getIndexer()->getName().'</strong>'
        ));

        $fieldset->addField('description', 'note', array(
            'label' => Mage::helper('Mage_Index_Helper_Data')->__('Index Description'),
            'title' => Mage::helper('Mage_Index_Helper_Data')->__('Index Description'),
            'text'  => $model->getIndexer()->getDescription()
        ));

        $fieldset->addField('mode', 'select', array(
            'label' => Mage::helper('Mage_Index_Helper_Data')->__('Index Mode'),
            'title' => Mage::helper('Mage_Index_Helper_Data')->__('Index Mode'),
            'name'  => 'mode',
            'value' => $model->getMode(),
            'values'=> $model->getModesOptions()
        ));

        //$form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Index_Helper_Data')->__('Process Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Mage_Index_Helper_Data')->__('Process Information');
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

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return true;
    }
}
