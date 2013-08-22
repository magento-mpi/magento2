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
 * Products grid for URL rewrites editing
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid extends Magento_Adminhtml_Block_Catalog_Product_Grid
{
    /**
     * Disable massaction
     *
     * @return Magento_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare columns layout
     *
     * @return Magento_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid
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
                'options' => Mage::getSingleton('Magento_Catalog_Model_Product_Status')->getOptionArray(),
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
     * @param Magento_Catalog_Model_Product|Magento_Object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('product' => $row->getId())) . 'category';
    }
}
