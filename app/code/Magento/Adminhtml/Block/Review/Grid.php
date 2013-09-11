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
 * Adminhtml reviews grid
 *
 * @method int getProductId() getProductId()
 * @method Magento_Adminhtml_Block_Review_Grid setProductId() setProductId(int $productId)
 * @method int getCustomerId() getCustomerId()
 * @method Magento_Adminhtml_Block_Review_Grid setCustomerId() setCustomerId(int $customerId)
 * @method Magento_Adminhtml_Block_Review_Grid setMassactionIdFieldOnlyIndexValue() setMassactionIdFieldOnlyIndexValue(bool $onlyIndex)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Review_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $storeManager, $urlModel, $data);
    }

    /**
     * Initialize grid
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('reviwGrid');
        $this->setDefaultSort('created_at');
    }

    /**
     * Save search results
     *
     * @return Magento_Backend_Block_Widget_Grid
     */
    protected function _afterLoadCollection()
    {
        /** @var $actionPager Magento_Review_Helper_Action_Pager */
        $actionPager = Mage::helper('Magento_Review_Helper_Action_Pager');
        $actionPager->setStorageId('reviews');
        $actionPager->setItems($this->getCollection()->getResultingIds());

        return parent::_afterLoadCollection();
    }

    /**
     * Prepare collection
     *
     * @return Magento_Adminhtml_Block_Review_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $model Magento_Review_Model_Review */
        $model = Mage::getModel('Magento_Review_Model_Review');
        /** @var $collection Magento_Review_Model_Resource_Review_Product_Collection */
        $collection = $model->getProductCollection();

        if ($this->getProductId() || $this->getRequest()->getParam('productId', false)) {
            $productId = $this->getProductId();
            if (!$productId) {
                $productId = $this->getRequest()->getParam('productId');
            }
            $this->setProductId($productId);
            $collection->addEntityFilter($this->getProductId());
        }

        if ($this->getCustomerId() || $this->getRequest()->getParam('customerId', false)) {
            $customerId = $this->getCustomerId();
            if (!$customerId){
                $customerId = $this->getRequest()->getParam('customerId');
            }
            $this->setCustomerId($customerId);
            $collection->addCustomerFilter($this->getCustomerId());
        }

        if ($this->_coreRegistry->registry('usePendingFilter') === true) {
            $collection->addStatusFilter($model->getPendingStatus());
        }

        $collection->addStoreData();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_Backend_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        /** @var $helper Magento_Review_Helper_Data */
        $helper = Mage::helper('Magento_Review_Helper_Data');
        $this->addColumn('review_id', array(
            'header'        => __('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'filter_index'  => 'rt.review_id',
            'index'         => 'review_id',
        ));

        $this->addColumn('created_at', array(
            'header'        => __('Created'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
            'filter_index'  => 'rt.created_at',
            'index'         => 'review_created_at',
        ));

        if (!$this->_coreRegistry->registry('usePendingFilter')) {
            $this->addColumn('status', array(
                'header'        => __('Status'),
                'align'         => 'left',
                'type'          => 'options',
                'options'       => $helper->getReviewStatuses(),
                'width'         => '100px',
                'filter_index'  => 'rt.status_id',
                'index'         => 'status_id',
            ));
        }

        $this->addColumn('title', array(
            'header'        => __('Title'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.title',
            'index'         => 'title',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('nickname', array(
            'header'        => __('Nickname'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.nickname',
            'index'         => 'nickname',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('detail', array(
            'header'        => __('Review'),
            'align'         => 'left',
            'index'         => 'detail',
            'filter_index'  => 'rdt.detail',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'    => __('Visibility'),
                'index'     => 'stores',
                'type'      => 'store',
                'store_view' => true,
            ));
        }

        $this->addColumn('type', array(
            'header'    => __('Type'),
            'type'      => 'select',
            'index'     => 'type',
            'filter'    => 'Magento_Adminhtml_Block_Review_Grid_Filter_Type',
            'renderer'  => 'Magento_Adminhtml_Block_Review_Grid_Renderer_Type'
        ));

        $this->addColumn('name', array(
            'header'    => __('Product'),
            'align'     =>'left',
            'type'      => 'text',
            'index'     => 'name',
            'escape'    => true
        ));

        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'align'     => 'right',
            'type'      => 'text',
            'width'     => '50px',
            'index'     => 'sku',
            'escape'    => true
        ));

        $this->addColumn('action',
            array(
                'header'    => __('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getReviewId',
                'actions'   => array(
                    array(
                        'caption' => __('Edit'),
                        'url'     => array(
                            'base'=>'*/catalog_product_review/edit',
                            'params'=> array(
                                'productId' => $this->getProductId(),
                                'customerId' => $this->getCustomerId(),
                                'ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : null
                            )
                         ),
                         'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false
        ));

        $this->addRssList('rss/catalog/review', __('Pending Reviews RSS'));

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid mass actions
     *
     * @return Magento_Backend_Block_Widget_Grid|void
     */
    protected function _prepareMassaction()
    {
        /** @var $helper Magento_Review_Helper_Data */
        $helper = Mage::helper('Magento_Review_Helper_Data');

        $this->setMassactionIdField('review_id');
        $this->setMassactionIdFilter('rt.review_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('reviews');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> __('Delete'),
            'url'  => $this->getUrl(
                '*/*/massDelete',
                array('ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index')
            ),
            'confirm' => __('Are you sure?')
        ));

        $statuses = $helper->getReviewStatusesOptionArray();
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('update_status', array(
            'label'         => __('Update Status'),
            'url'           => $this->getUrl(
                '*/*/massUpdateStatus',
                array('ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index')
            ),
            'additional'    => array(
                'status'    => array(
                    'name'      => 'status',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => __('Status'),
                    'values'    => $statuses
                )
            )
        ));
    }

    /**
     * Get row url
     *
     * @param Magento_Review_Model_Review|Magento_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product_review/edit', array(
            'id' => $row->getReviewId(),
            'productId' => $this->getProductId(),
            'customerId' => $this->getCustomerId(),
            'ret'       => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : null,
        ));
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        if( $this->getProductId() || $this->getCustomerId() ) {
            return $this->getUrl(
                '*/catalog_product_review/' . ($this->_coreRegistry->registry('usePendingFilter') ? 'pending' : ''),
                array(
                    'productId' => $this->getProductId(),
                    'customerId' => $this->getCustomerId(),
                )
            );
        } else {
            return $this->getCurrentUrl();
        }
    }
}
