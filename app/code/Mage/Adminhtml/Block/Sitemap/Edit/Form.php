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
 * Sitemap edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sitemap_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sitemap_form');
        $this->setTitle(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Sitemap Information'));
    }


    protected function _prepareForm()
    {
        $model = Mage::registry('sitemap_sitemap');

        $form = new Magento_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('add_sitemap_form', array('legend' => Mage::helper('Mage_Sitemap_Helper_Data')->__('Sitemap')));

        if ($model->getId()) {
            $fieldset->addField('sitemap_id', 'hidden', array(
                'name' => 'sitemap_id',
            ));
        }

        $fieldset->addField('sitemap_filename', 'text', array(
            'label' => Mage::helper('Mage_Sitemap_Helper_Data')->__('Filename'),
            'name'  => 'sitemap_filename',
            'required' => true,
            'note'  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('example: sitemap.xml'),
            'value' => $model->getSitemapFilename()
        ));

        $fieldset->addField('sitemap_path', 'text', array(
            'label' => Mage::helper('Mage_Sitemap_Helper_Data')->__('Path'),
            'name'  => 'sitemap_path',
            'required' => true,
            'note'  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('example: "sitemap/" or "/" for base path (path must be writeable)'),
            'value' => $model->getSitemapPath()
        ));

        if (!Mage::app()->hasSingleStore()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label'    => Mage::helper('Mage_Sitemap_Helper_Data')->__('Store View'),
                'title'    => Mage::helper('Mage_Sitemap_Helper_Data')->__('Store View'),
                'name'     => 'store_id',
                'required' => true,
                'value'    => $model->getStoreId(),
                'values'   => Mage::getSingleton('Mage_Core_Model_System_Store')->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()->createBlock('Mage_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'     => 'store_id',
                'value'    => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
