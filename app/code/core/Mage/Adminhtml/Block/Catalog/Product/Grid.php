<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
            ->addAttributeToSelect('attribute_set_id')
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
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId);
        }
        else {
            $collection->addAttributeToSelect('status');
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
#print_r($this->getCollection()->getSelect()->__toString());
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

        $storeId = $this->getRequest()->getParam('store', 0);
        if ((int) $storeId) {
            $store = Mage::getModel('core/store')->load($storeId);
            $this->addColumn('custom_name',
                array(
                    'header'=> __('Name In %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getConfig()->getId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> __('Attrib. Set Name'),
                'width' => '130px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

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
                'currency_code' => (string) Mage::getStoreConfig('general/currency/base'),
                'index' => 'price',
        ));


        $this->addColumn('qty',
            array(
                'header'=> __('Qty'),
                'width' => '130px',
                'type'  => 'number',
                'index' => 'qty',
        ));

        $statuses = Mage::getResourceModel('catalog/product_status_collection')
            ->load()
            ->toOptionHash();

        $this->addColumn('status',
            array(
                'header'=> __('Status'),
                'width' => '90px',
                'index' => 'status',
                'type'  => 'options',
                'options' => $statuses,
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

        //$this->addExportType('*/*/exportCsv', __('CSV'));
        //$this->addExportType('*/*/exportXml', __('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('id'=>$row->getId(), 'store'=>$this->getRequest()->getParam('store',0)));
    }
}
