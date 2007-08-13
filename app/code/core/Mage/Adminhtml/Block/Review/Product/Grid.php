<?php
/**
 * Adminhtml product grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Review_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setRowClickCallback('review.gridRowClick');
        $this->setUseAjax(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header'=> __('ID'),
                'width' => '50px',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> __('Name'),
                'index' => 'name',
        ));

        if ((int) $this->getRequest()->getParam('store', 0)) {
            $this->addColumn('custom_name',
                array(
                    'header'=> __('Name In Store'),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('sku',
            array(
                'header'=> __('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));
        $this->addColumn('price',
            array(
                'header'=> __('Price'),
                'type'  => 'currency',
                'index' => 'price',
        ));
        $this->addColumn('qty',
            array(
                'header'=> __('Qty'),
                'width' => '130px',
                'type'  => 'number',
                'index' => 'qty',
        ));
        $this->addColumn('status',
            array(
                'header'=> __('Status'),
                'width' => '50px',
                'index' => 'status',
        ));

        $this->addColumn('stores',
            array(
                'header'=> __('Stores'),
                'width' => '100px',
                'filter'    => 'adminhtml/catalog_product_grid_filter_store',
                'renderer'  => 'adminhtml/catalog_product_grid_renderer_store',
                'sortable'  => false,
                'index'     => 'stores',
       ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/jsonProductInfo', array('id' => $row->getId()));
    }
}