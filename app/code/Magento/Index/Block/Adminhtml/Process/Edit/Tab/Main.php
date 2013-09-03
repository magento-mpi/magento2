<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Index_Block_Adminhtml_Process_Edit_Tab_Main
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
            array('legend'=>__('General'), 'class'=>'fieldset-wide')
        );

        $fieldset->addField('process_id', 'hidden', array('name' => 'process', 'value'=>$model->getId()));

        $fieldset->addField('name', 'note', array(
            'label' => __('Index Name'),
            'title' => __('Index Name'),
            'text'  => '<strong>'.$model->getIndexer()->getName().'</strong>'
        ));

        $fieldset->addField('description', 'note', array(
            'label' => __('Index Description'),
            'title' => __('Index Description'),
            'text'  => $model->getIndexer()->getDescription()
        ));

        $fieldset->addField('mode', 'select', array(
            'label' => __('Index Mode'),
            'title' => __('Index Mode'),
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
        return __('Process Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Process Information');
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
