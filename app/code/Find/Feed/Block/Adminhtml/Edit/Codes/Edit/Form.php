<?php
/**
 * {license_notice}
 *
 * @category
 * @package     _home
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Attribute map edit form container block
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Find_Feed_Block_Adminhtml_Edit_Codes_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Import codes list
     *
     * @return array
     */
    protected function _getImportCodeList()
    {
        $attributes = Mage::getConfig()->getNode(Find_Feed_Model_Import::XML_NODE_FIND_FEED_ATTRIBUTES)->children();
        $result     = array();
        foreach ($attributes as $node) {
            $label = trim((string)$node->label);
            if ($label) {
                $result[$label] = $label;
            }
        }

        return $result;
    }

    /**
     * Magento entity type list for eav attributes
     *
     * @return array
     */
    protected function _getCatalogEntityType()
    {
        return Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('catalog_product')->getId();
    }


    /**
     * Magento eav attributes list
     *
     * @return array
     */
    protected function _getEavAttributeList()
    {
        $result     = array();
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Attribute_Collection');
        foreach ($collection as $model) {
            $result[$model->getAttributeCode()] = $model->getAttributeCode();
        }
        return $result;
    }

    /**
     * Prepare form
     *
     * @return Magento_Object
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
            'id'        => 'import_item_form',
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('generate_fieldset', array(
            'legend' => Mage::helper('Find_Feed_Helper_Data')->__('Item params')
        ));
        $fieldset->addField('import_code', 'select', array(
            'label'     => Mage::helper('Find_Feed_Helper_Data')->__('Import code'),
            'name'      => 'import_code',
            'required'  => true,
            'options'   => $this->_getImportCodeList()
        ));
        $fieldset->addField('eav_code', 'select', array(
            'label'     => Mage::helper('Find_Feed_Helper_Data')->__('Eav code'),
            'name'      => 'eav_code',
            'required'  => true,
            'options'   => $this->_getEavAttributeList()
        ));

        $source = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Source_Boolean');
        $isImportedOptions = $source->getOptionArray();

        $fieldset->addField('is_imported', 'select', array(
            'label'     => Mage::helper('Find_Feed_Helper_Data')->__('Is imported'),
            'name'      => 'is_imported',
            'value'     => 1,
            'options'   => $isImportedOptions
        ));
        $form->setUseContainer(true);

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
