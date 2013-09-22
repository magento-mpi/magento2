<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Block\Adminhtml\Banner;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Set defaults
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('bannerGrid');
        $this->setDefaultSort('banner_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('banner_filter');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return \Magento\Banner\Block\Adminhtml\Banner\Grid
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getResourceModel('Magento\Banner\Model\Resource\Banner\Collection')
            ->addStoresVisibility();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn('banner_id',
            array(
                'header'=> __('ID'),
                'width' => 1,
                'type'  => 'number',
                'index' => 'banner_id',
        ));

        $this->addColumn('banner_name', array(
            'header' => __('Banner'),
            'type'   => 'text',
            'index'  => 'name',
            'escape' => true
        ));

        $this->addColumn('banner_types', array(
            'header'  => __('Banner Types'),
            'type'    => 'options',
            'options' => \Mage::getSingleton('Magento\Banner\Model\Config')->toOptionArray(true, false),
            'index'   => 'types',
            'width'   => 250,
            'filter'  => false, // TODO implement
        ));

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'                => __('Visibility'),
                'type'                  => 'store',
                'index'                 => 'stores',
                'sortable'              => false,
                'store_view'            => true,
                'width'                 => 200
            ));
        }

        $this->addColumn('banner_is_enabled',
            array(
                'header'    => __('Active'),
                'align'     => 'center',
                'width'     => 1,
                'index'     => 'is_enabled',
                'type'      => 'options',
                'options'   => array(
                    \Magento\Banner\Model\Banner::STATUS_ENABLED  =>
                        __('Yes'),
                    \Magento\Banner\Model\Banner::STATUS_DISABLED =>
                        __('No'),
                ),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action options for this grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('banner');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => __('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  =>
                __('Are you sure you want to delete these banners?')
        ));

        return $this;
    }

    /**
     * Grid row URL getter
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getBannerId()));
    }

    /**
     * Define row click callback
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Add store filter
     *
     * @param \Magento\Adminhtml\Block\Widget\Grid\Column  $column
     * @return \Magento\Banner\Block\Adminhtml\Banner\Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getIndex() == 'stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
