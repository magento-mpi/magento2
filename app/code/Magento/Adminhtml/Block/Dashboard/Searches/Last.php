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
 * Adminhtml dashboard last search keywords block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Dashboard_Searches_Last extends Magento_Adminhtml_Block_Dashboard_Grid
{
    protected $_collection;

    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastSearchGrid');
    }

    protected function _prepareCollection()
    {
        if (!Mage::helper('Magento_Core_Helper_Data')->isModuleEnabled('Magento_CatalogSearch')) {
            return parent::_prepareCollection();
        }
        $this->_collection = Mage::getModel('Magento_CatalogSearch_Model_Query')
            ->getResourceCollection();
        $this->_collection->setRecentQueryFilter();

        if ($this->getRequest()->getParam('store')) {
            $this->_collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')){
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getRequest()->getParam('group')){
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', array('in' => $storeIds));
        }

        $this->setCollection($this->_collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('search_query', array(
            'header'    => __('Search Term'),
            'sortable'  => false,
            'index'     => 'query_text',
            'renderer'  => 'Magento_Adminhtml_Block_Dashboard_Searches_Renderer_Searchquery',
        ));

        $this->addColumn('num_results', array(
            'header'    => __('Results'),
            'sortable'  => false,
            'index'     => 'num_results',
            'type'      => 'number'
        ));

        $this->addColumn('popularity', array(
            'header'    => __('Uses'),
            'sortable'  => false,
            'index'     => 'popularity',
            'type'      => 'number'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_search/edit', array('id'=>$row->getId()));
    }
}
