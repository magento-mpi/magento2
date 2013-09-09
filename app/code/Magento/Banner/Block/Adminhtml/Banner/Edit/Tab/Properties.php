<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Main banner properties edit form
 *
 * @category   Magento
 * @package    Magento_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Properties extends Magento_Backend_Block_Widget_Form_Generic
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Set form id prefix, declare fields for banner properties
     *
     * @return Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Properties
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'banner_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $model = Mage::registry('current_banner');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>__('Banner Properties'))
        );

        if ($model->getBannerId()) {
            $fieldset->addField('banner_id', 'hidden', array(
                'name' => 'banner_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'label'     => __('Banner Name'),
            'name'      => 'name',
            'required'  => true,
            'disabled'  => (bool)$model->getIsReadonly()
        ));

        $fieldset->addField('is_enabled', 'select', array(
            'label'     => __('Active'),
            'name'      => 'is_enabled',
            'required'  => true,
            'disabled'  => (bool)$model->getIsReadonly(),
            'options'   => array(
                Magento_Banner_Model_Banner::STATUS_ENABLED  =>
                    __('Yes'),
                Magento_Banner_Model_Banner::STATUS_DISABLED =>
                    __('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_enabled', Magento_Banner_Model_Banner::STATUS_ENABLED);
        }

        // whether to specify banner types - for UI design purposes only
        $fieldset->addField('is_types', 'select', array(
            'label'     => __('Applies To'),
            'options'   => array(
                    '0' => __('Any Banner Type'),
                    '1' => __('Specified Banner Types'),
                ),
            'disabled'  => (bool)$model->getIsReadonly(),
        ));
        $model->setIsTypes((string)(int)$model->getTypes()); // see $form->setValues() below

        $fieldset->addField('types', 'multiselect', array(
            'label'     => __('Specify Types'),
            'name'      => 'types',
            'disabled'  => (bool)$model->getIsReadonly(),
            'values'    => Mage::getSingleton('Magento_Banner_Model_Config')->toOptionArray(false, false),
            'can_be_empty' => true,
        ));

        $afterFormBlock = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Form_Element_Dependence')
            ->addFieldMap("{$htmlIdPrefix}is_types", 'is_types')
            ->addFieldMap("{$htmlIdPrefix}types", 'types')
            ->addFieldDependence('types', 'is_types', '1');

        $this->_eventManager->dispatch('banner_edit_tab_properties_after_prepare_form', array(
            'model' => $model,
            'form' => $form,
            'block' => $this,
            'after_form_block' => $afterFormBlock,
        ));

        $this->setChild('form_after', $afterFormBlock);

        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Banner Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
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
}
