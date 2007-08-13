<?php
/**
 * Adminhtml customer grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('qty')
            ->addAttributeToSelect('price')
            ->joinField('store_id',
                'catalog/product_store',
                'store_id',
                'product_id=entity_id',
                '{{table}}.store_id='.$storeId)
            ->joinField('stores',
                'catalog/product_store',
                'store_id',
                'product_id=entity_id',
                null,
                'left');

        if ($storeId) {
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId);
        }

        $collection->getEntity()->setStore(0);
        $this->setCollection($collection);

        $filter = $this->getRequest()->getParam($this->getVarNameFilter());
        if (empty($filter)) {
            $this->_setFilterValues(array('stores'=>$this->getParam('store', 0)));
            $this->getColumn('stores')->getFilter()->setValue(null);
        }

        parent::_prepareCollection();

        $this->getCollection()->addStoreNamesToResult();
        return $this;
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
        /*$this->addColumn('rating',
            array(
                'header'=> __('Rating'),
                'width' => '100px',
                'index' => 'rating',
        ));*/
        /*$this->addColumn('category',
            array(
                'header'=> __('Categories'),
                'width' => '150px',
                'filter'=> false,
                'index' => 'category',
        ));*/
        $this->addColumn('stores',
            array(
                'header'=> __('Stores'),
                'width' => '100px',
                'filter'    => 'adminhtml/catalog_product_grid_filter_store',
                'renderer'  => 'adminhtml/catalog_product_grid_renderer_store',
                'sortable'  => false,
                'index'     => 'stores',
        ));

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('id'=>$row->getId(), 'store'=>$this->getRequest()->getParam('store',0)));
    }
}
