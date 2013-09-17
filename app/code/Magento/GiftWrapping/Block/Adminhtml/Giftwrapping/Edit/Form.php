<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftWrapping_Block_Adminhtml_Giftwrapping_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magento_giftwrapping_form');
        $this->setTitle(__('Gift Wrapping Information'));
    }

    /**
     * Prepares layout and set element renderer
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        Magento_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Magento_GiftWrapping_Block_Adminhtml_Giftwrapping_Form_Renderer_Element',
                $this->getNameInLayout() . '_element_gift_wrapping'
            )
        );
    }

    /**
     * Prepare edit form
     *
     * @return Magento_GiftWrapping_Block_Adminhtml_Giftwrapping_Edit_Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_giftwrapping_model');

        $actionParams = array('store' => $model->getStoreId());
        if ($model->getId()) {
            $actionParams['id'] = $model->getId();
        }
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', $actionParams),
                'method' => 'post',
                'field_name_suffix' => 'wrapping',
                'enctype'=> 'multipart/form-data',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>__('Gift Wrapping Information')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField('design', 'text', array(
            'label'    => __('Gift Wrapping Design'),
            'name'     => 'design',
            'required' => true,
            'value'    => $model->getDesign(),
            'scope'    => 'store'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids',
                'required' => true,
                'label'    => __('Websites'),
                'values'   => Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteValuesForForm(),
                'value'    => $model->getWebsiteIds(),
            ));
            $renderer = $this->getLayout()->createBlock(
                'Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element'
            );
            $field->setRenderer($renderer);
        }

        $fieldset->addField('status', 'select', array(
            'label'    => __('Status'),
            'name'     => 'status',
            'required' => true,
            'options'  => array(
                '1' => __('Enabled'),
                '0' => __('Disabled'),
            )
        ));

        $fieldset->addType('price', 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Price');
        $fieldset->addField('base_price', 'price', array(
            'label'    => __('Price'),
            'name'     => 'base_price',
            'required' => true,
            'class'    => 'validate-not-negative-number',
            'after_element_html' => '<strong>[' .  Mage::app()->getBaseCurrencyCode() . ']</strong>'
        ));

        $uploadButton = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label' => __('Upload File'),
                'id' => 'upload_image_button',
                'onclick' => 'uploadImagesForPreview()'
            ));

        $fieldset->addField('image', 'image', array(
                'label' => __('Image'),
                'name'  => 'image_name',
                'after_element_html' => $uploadButton->toHtml()
             )
        );

        if (!$model->getId()) {
            $model->setData('status', '1');
        }

        if ($model->hasTmpImage()) {
            $fieldset->addField('tmp_image', 'hidden', array(
                'name' => 'tmp_image',
            ));
        }
        $this->setForm($form);
        $form->setValues($model->getData());
        $form->setDataObject($model);
        $form->setUseContainer(true);
        return parent::_prepareForm();
    }

    /**
     * Retrieve Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array('image' => 'Magento_GiftWrapping_Block_Adminhtml_Giftwrapping_Helper_Image');
    }
}
