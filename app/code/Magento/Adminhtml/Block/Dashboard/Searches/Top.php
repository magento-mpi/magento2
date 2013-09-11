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

namespace Magento\Adminhtml\Block\Dashboard\Searches;

class Top extends \Magento\Adminhtml\Block\Dashboard\Grid
{
    protected $_collection;

    protected function _construct()
    {
        parent::_construct();
        $this->setId('topSearchGrid');
    }

    protected function _prepareCollection()
    {
        if (!\Mage::helper('Magento\Core\Helper\Data')->isModuleEnabled('Magento_CatalogSearch')) {
            return parent::_prepareCollection();
        }
        $this->_collection = \Mage::getModel('Magento\CatalogSearch\Model\Query')
            ->getResourceCollection();

        if ($this->getRequest()->getParam('store')) {
            $storeIds = $this->getRequest()->getParam('store');
        } else if ($this->getRequest()->getParam('website')){
            $storeIds = \Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        } else if ($this->getRequest()->getParam('group')){
            $storeIds = \Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        } else {
            $storeIds = '';
        }

        $this->_collection
            ->setPopularQueryFilter($storeIds);

        $this->setCollection($this->_collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('search_query', array(
            'header'    => __('Search Term'),
            'sortable'  => false,
            'index'     => 'name',
            'renderer'  => '\Magento\Adminhtml\Block\Dashboard\Searches\Renderer\Searchquery',
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
