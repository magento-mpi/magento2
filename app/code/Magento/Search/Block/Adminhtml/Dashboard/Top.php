<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block\Adminhtml\Dashboard;

/**
 *  Dashboard last search keywords block
 */
class Top extends \Magento\Backend\Block\Dashboard\Grid
{
    /**
     * @var \Magento\Search\Model\Resource\Query\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Search\Model\Resource\Query\CollectionFactory
     */
    protected $_queriesFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /** @var string */
    protected $_template = 'Magento_Backend::dashboard/grid.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Search\Model\Resource\Query\CollectionFactory $queriesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Search\Model\Resource\Query\CollectionFactory $queriesFactory,
        array $data = array()
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_queriesFactory = $queriesFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('topSearchGrid');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $this->_collection = $this->_queriesFactory->create();

        if ($this->getRequest()->getParam('store')) {
            $storeIds = $this->getRequest()->getParam('store');
        } else if ($this->getRequest()->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        } else {
            $storeIds = '';
        }

        $this->_collection->setPopularQueryFilter($storeIds);

        $this->setCollection($this->_collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'search_query',
            array(
                'header' => __('Search Term'),
                'sortable' => false,
                'index' => 'name',
                'renderer' => 'Magento\Backend\Block\Dashboard\Searches\Renderer\Searchquery'
            )
        );

        $this->addColumn(
            'num_results',
            array('header' => __('Results'), 'sortable' => false, 'index' => 'num_results', 'type' => 'number')
        );

        $this->addColumn(
            'popularity',
            array('header' => __('Uses'), 'sortable' => false, 'index' => 'popularity', 'type' => 'number')
        );

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('search/term/edit', array('id' => $row->getId()));
    }
}
