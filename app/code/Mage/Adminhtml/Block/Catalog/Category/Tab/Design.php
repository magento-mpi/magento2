<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Catalog_Category_Tab_Design extends Mage_Adminhtml_Block_Catalog_Form
{
    public function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = Mage::registry('category');
        }
        return $this->_category;
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Magento_Data_Form();
        $form->setDataObject($this->getCategory());

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('Mage_Catalog_Helper_Data')->__('Custom Design')));


        $this->_setFieldset($this->getCategory()->getDesignAttributes(), $fieldset);

        $form->addValues($this->getCategory()->getData());
        $form->setFieldNameSuffix('general');
        $this->setForm($form);
    }

}
