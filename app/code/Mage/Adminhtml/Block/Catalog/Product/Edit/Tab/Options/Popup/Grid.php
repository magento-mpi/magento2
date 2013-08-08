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
 * Adminhtml product grid in custom options popup
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Popup_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    /**
     * Return empty row url for disabling JS click events
     *
     * @param Mage_Catalog_Model_Product|Magento_Object
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRowUrl($row)
    {
        return null;
    }

    /**
     * Remove some grid columns for product grid in popup
     */
    public function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->removeColumn('action');
        $this->removeColumn('status');
        $this->removeColumn('visibility');
        $this->clearRss();
    }

    /**
     * Add import action to massaction block
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Popup_Grid
     */
    public function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()
            ->setFormFieldName('product')
            ->addItem('import', array('label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Import')));

        return $this;
    }

    /**
     * Define grid update URL for ajax queries
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/optionsimportgrid');
    }
}
