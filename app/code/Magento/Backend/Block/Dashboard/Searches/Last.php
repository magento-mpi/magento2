<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard last search keywords block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Dashboard\Searches;

class Last extends \Magento\Backend\Block\Dashboard\Grid
{
    protected $_collection;

    /**
     * @var \Magento\CatalogSearch\Model\Resource\Query\CollectionFactory
     */
    protected $_queriesFactory;

    /**
     * @var \Magento\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\CatalogSearch\Model\Resource\Query\CollectionFactory $queriesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Module\Manager $moduleManager,
        \Magento\CatalogSearch\Model\Resource\Query\CollectionFactory $queriesFactory,
        array $data = array()
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_queriesFactory = $queriesFactory;
        parent::__construct($context, $urlModel, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastSearchGrid');
    }

    protected function _prepareCollection()
    {
        if (!$this->_moduleManager->isEnabled('Magento_CatalogSearch')) {
            return parent::_prepareCollection();
        }
        $this->_collection = $this->_queriesFactory->create();
        $this->_collection->setRecentQueryFilter();

        if ($this->getRequest()->getParam('store')) {
            $this->_collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
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
            'renderer'  => 'Magento\Backend\Block\Dashboard\Searches\Renderer\Searchquery',
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
        return $this->getUrl('catalog/search/edit', array('id'=>$row->getId()));
    }
}
