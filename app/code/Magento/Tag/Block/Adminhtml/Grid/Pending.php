<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml pending tags grid
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Grid_Pending extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Tag data
     *
     * @var Magento_Tag_Helper_Data
     */
    protected $_tagData = null;

    /**
     * @param Magento_Tag_Helper_Data $tagData
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Tag_Helper_Data $tagData,
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_tagData = $tagData;
        parent::__construct($backendData, $coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pending_grid')
             ->setDefaultSort('name')
             ->setDefaultDir('ASC')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_Tag_Model_Resource_Tag_Collection')
            ->addSummary(0)
            ->addStoresVisibility()
            ->addStatusFilter(Magento_Tag_Model_Tag::STATUS_PENDING);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('name', array(
            'header'        => __('Tag'),
            'index'         => 'name'
        ));

        $this->addColumn('products', array(
            'header'        => __('Products'),
            'width'         => '140px',
            'align'         => 'right',
            'index'         => 'products',
            'type'          => 'number'
        ));

        $this->addColumn('customers', array(
            'header'        => __('Customers'),
            'width'         => '140px',
            'align'         => 'right',
            'index'         => 'customers',
            'type'          => 'number'
        ));

        // Collection for stores filters
        if (!$collection = Mage::registry('stores_select_collection')) {
            $collection =  Mage::app()->getStore()->getResourceCollection()
                ->load();
            Mage::register('stores_select_collection', $collection);
        }

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'    => __('Store View'),
                'type'      => 'store',
                'index'     => 'stores',
                'sortable'  => false,
                'store_view'=> true
            ));
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrives row click URL
     *
     * @param  mixed $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('tag_id' => $row->getId(), 'ret' => 'pending'));
    }

    protected function _addColumnFilterToCollection($column)
    {
        if($column->getIndex() == 'stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('tag_id');
        $this->getMassactionBlock()->setFormFieldName('tag');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> __('Delete'),
             'url'  => $this->getUrl('*/*/massDelete', array('ret' => 'pending')),
             'confirm' => __('Are you sure?')
        ));

        $statuses = $this->_tagData->getStatusesOptionsArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));

        $this->getMassactionBlock()->addItem('status', array(
             'label'=> __('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true, 'ret' => 'pending')),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => __('Status'),
                         'values' => $statuses
                     )
             )
        ));

        return $this;
    }

    /*
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/tag/ajaxPendingGrid', array('_current' => true));
    }
}
