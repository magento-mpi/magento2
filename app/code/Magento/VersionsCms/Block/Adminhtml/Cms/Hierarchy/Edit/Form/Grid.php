<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form;

/**
 * Cms Hierarchy Pages Tree Edit Cms Page Grid Block
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Cms\Model\Resource\Page\CollectionFactory
     */
    protected $_pageCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Cms\Model\Resource\Page\CollectionFactory $pageCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Cms\Model\Resource\Page\CollectionFactory $pageCollectionFactory,
        array $data = array()
    ) {
        $this->_pageCollectionFactory = $pageCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize Grid block
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setRowClickCallback('hierarchyNodes.pageGridRowClick.bind(hierarchyNodes)');
        $this->setCheckboxCheckCallback('hierarchyNodes.checkCheckboxes.bind(hierarchyNodes)');
        $this->setDefaultSort('page_id');
        $this->setMassactionIdField('page_id');
        $this->setUseAjax(true);
        $this->setId('cms_page_grid');
    }

    /**
     * Prepare Cms Page Collection for Grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_pageCollectionFactory->create();

        $store = $this->_getStore();
        if ($store->getId()) {
            $collection->addStoreFilter($store);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'is_selected',
            array(
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select',
                'type' => 'checkbox',
                'index' => 'page_id',
                'filter' => false
            )
        );
        $this->addColumn(
            'page_id',
            array(
                'header' => __('Page ID'),
                'header_css_class' => 'col-page-id col-id',
                'column_css_class' => 'col-page-id col-id',
                'sortable' => true,
                'type' => 'range',
                'index' => 'page_id'
            )
        );

        $this->addColumn(
            'title',
            array(
                'header' => __('Title'),
                'header_css_class' => 'col-title',
                'column_css_class' => 'col-title label',
                'index' => 'title'
            )
        );

        $this->addColumn(
            'identifier',
            array(
                'header' => __('URL Key'),
                'header_css_class' => 'col-identifier',
                'column_css_class' => 'col-identifier identifier',
                'index' => 'identifier'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Grid Reload URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/*/pageGrid', array('_current' => true));
    }

    /**
     * Get selected store by store id passed through query.
     *
     * @return \Magento\Store\Model\Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
}
