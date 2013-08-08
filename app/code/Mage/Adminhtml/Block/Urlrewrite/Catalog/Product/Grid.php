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
 * Products grid for URL rewrites editing
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    /**
     * Disable massaction
     *
     * @return Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare columns layout
     *
     * @return Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> __('ID'),
                'width' => 50,
                'index' => 'entity_id',
        ));

        $this->addColumn('name',
            array(
                'header'=> __('Name'),
                'index' => 'name',
        ));

        $this->addColumn('sku',
            array(
                'header'=> __('SKU'),
                'width' => 80,
                'index' => 'sku',
        ));
        $this->addColumn('status',
            array(
                'header'=> __('Status'),
                'width' => 50,
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('Mage_Catalog_Model_Product_Status')->getOptionArray(),
        ));
        return $this;
    }

    /**
     * Get URL for dispatching grid ajax requests
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current' => true));
    }

    /**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Varien_Object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('product' => $row->getId())) . 'category';
    }
}
