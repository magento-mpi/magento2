<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Intialize form
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('enterprise_giftwrapping_form');
        $this->setTitle(Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Gift Wrapping Information'));
    }

    /**
     * Prepares layout and set element renderer
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Form_Renderer_Element'
            )
        );
    }

    /**
     * Prepare edit form
     *
     * @return Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Edit_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_giftwrapping_model');

        $actionParams = array('store' => $model->getStoreId());
        if ($model->getId()) {
            $actionParams['id'] = $model->getId();
        }
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', $actionParams),
            'method' => 'post',
            'field_name_suffix' => 'wrapping',
            'enctype'=> 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Gift Wrapping Information')));
        $this->_addElementTypes($fieldset);

        $fieldset->addField('design', 'text', array(
            'label'    => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Gift Wrapping Design'),
            'name'     => 'design',
            'required' => true,
            'value'    => $model->getDesign(),
            'scope'    => 'store'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids','multiselect',array(
                'name'     => 'website_ids',
                'required' => true,
                'label'    => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Websites'),
                'values'   => Mage::getSingleton('Mage_Adminhtml_Model_System_Store')->getWebsiteValuesForForm(),
                'value'    => $model->getWebsiteIds()
            ));
        }

        $fieldset->addField('status', 'select', array(
            'label'    => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Status'),
            'name'     => 'status',
            'required' => true,
            'options'  => array(
                '1' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Enabled'),
                '0' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Disabled'),
            )
        ));

        $fieldset->addType('price', 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price');
        $fieldset->addField('base_price', 'price', array(
            'label'    => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Price'),
            'name'     => 'base_price',
            'required' => true,
            'class'    => 'validate-not-negative-number',
            'after_element_html' => '<br /><strong>[' .  Mage::app()->getBaseCurrencyCode() . ']</strong>'
        ));

        $uploadButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Upload File'),
                'id' => 'upload_image_button',
                'onclick' => 'uploadImagesForPreview()'
            ));

        $fieldset->addField('image', 'image', array(
                'label' => Mage::helper('Enterprise_GiftWrapping_Helper_Data')->__('Image'),
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
        return array(
            'image' => Mage::getConfig()->getBlockClassName(
                'Enterprise_GiftWrapping_Block_Adminhtml_Giftwrapping_Helper_Image'
            )
        );
    }
}
