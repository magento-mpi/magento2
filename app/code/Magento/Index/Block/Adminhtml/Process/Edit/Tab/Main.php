<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Index\Block\Adminhtml\Process\Edit\Tab;

use Magento\Backend\Block\Widget\Form;

class Main
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_index_process');
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('index_process_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend'=>__('General'), 'class'=>'fieldset-wide')
        );

        $fieldset->addField('process_id', 'hidden', array('name' => 'process', 'value' => $model->getId()));

        $fieldset->addField('name', 'note', array(
            'label' => __('Index Name'),
            'title' => __('Index Name'),
            'text'  => '<strong>' . $model->getIndexer()->getName() . '</strong>'
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
