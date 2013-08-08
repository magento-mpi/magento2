<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tag edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Search_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Init Form properties
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_search_form');
        $this->setTitle(Mage::helper('Mage_Catalog_Helper_Data')->__('Search Information'));
    }

    /**
     * Prepare form fields
     *
     * @return Magento_Adminhtml_Block_Catalog_Search_Edit_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_catalog_search');
        /* @var $model Mage_CatalogSearch_Model_Query */

        $form = new Magento_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('Mage_Catalog_Helper_Data')->__('General Information')));

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Yes')
            ));

        if ($model->getId()) {
            $fieldset->addField('query_id', 'hidden', array(
                'name'      => 'query_id',
            ));
        }

        $fieldset->addField('query_text', 'text', array(
            'name'      => 'query_text',
            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Search Query'),
            'title'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Search Query'),
            'required'  => true,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Store'),
                'title'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Store'),
                'values'    => Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreValuesForForm(true, false),
                'required'  => true,
            ));
            $renderer = $this->getLayout()->createBlock('Mage_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id'
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        if ($model->getId()) {
            $fieldset->addField('num_results', 'text', array(
                'name'     => 'num_results',
                'label'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Number of results'),
                'title'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Number of results (For the last time placed)'),
                'note'     => Mage::helper('Mage_Catalog_Helper_Data')->__('For the last time placed.'),
                'required' => true,
            ));

            $fieldset->addField('popularity', 'text', array(
                'name'     => 'popularity',
                'label'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Number of Uses'),
                'title'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Number of Uses'),
                'required' => true,
            ));
        }

        $fieldset->addField('synonym_for', 'text', array(
            'name'  => 'synonym_for',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Synonym For'),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Synonym For'),
            'note'  => Mage::helper('Mage_Catalog_Helper_Data')->__('Will make search for the query above return results for this search'),
        ));

        $fieldset->addField('redirect', 'text', array(
            'name'  => 'redirect',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Redirect URL'),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Redirect URL'),
            'class' => 'validate-url',
            'note'  => Mage::helper('Mage_Catalog_Helper_Data')->__('ex. http://domain.com'),
        ));

        $fieldset->addField('display_in_terms', 'select', array(
            'name'   => 'display_in_terms',
            'label'  => Mage::helper('Mage_Catalog_Helper_Data')->__('Display in Suggested Terms'),
            'title'  => Mage::helper('Mage_Catalog_Helper_Data')->__('Display in Suggested Terms'),
            'values' => $yesno,
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
