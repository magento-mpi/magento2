<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Block\Adminhtml\Giftwrapping\Edit;

class Form extends \Magento\Adminhtml\Block\Widget\Form
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

        \Magento\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Magento\GiftWrapping\Block\Adminhtml\Giftwrapping\Form\Renderer\Element',
                $this->getNameInLayout() . '_element_gift_wrapping'
            )
        );
    }

    /**
     * Prepare edit form
     *
     * @return \Magento\GiftWrapping\Block\Adminhtml\Giftwrapping\Edit\Form
     */
    protected function _prepareForm()
    {
        $model = \Mage::registry('current_giftwrapping_model');

        $actionParams = array('store' => $model->getStoreId());
        if ($model->getId()) {
            $actionParams['id'] = $model->getId();
        }
        $form = new \Magento\Data\Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', $actionParams),
            'method' => 'post',
            'field_name_suffix' => 'wrapping',
            'enctype'=> 'multipart/form-data'
        ));

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

        if (!\Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids',
                'required' => true,
                'label'    => __('Websites'),
                'values'   => \Mage::getSingleton('Magento\Core\Model\System\Store')->getWebsiteValuesForForm(),
                'value'    => $model->getWebsiteIds(),
            ));
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
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

        $fieldset->addType('price', 'Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Price');
        $fieldset->addField('base_price', 'price', array(
            'label'    => __('Price'),
            'name'     => 'base_price',
            'required' => true,
            'class'    => 'validate-not-negative-number',
            'after_element_html' => '<strong>[' .  \Mage::app()->getBaseCurrencyCode() . ']</strong>'
        ));

        $uploadButton = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')
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
        return array('image' => 'Magento\GiftWrapping\Block\Adminhtml\Giftwrapping\Helper\Image');
    }
}
