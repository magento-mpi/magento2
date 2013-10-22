<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Integration;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
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
        $this->setId('integrationGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('integration_filter');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return \Magento\Banner\Block\Adminhtml\Banner\Grid
     */
    protected function _prepareCollection()
    {
        /*
        $collection = $this->_bannerColFactory->create()
            ->addStoresVisibility();
        $this->setCollection($collection);
        */
        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> __('ID'),
                'width' => 1,
                'type'  => 'number',
                'index' => 'banner_id',
        ));

        $this->addColumn('integration_name', array(
            'header' => __('Integration'),
            'type'   => 'text',
            'index'  => 'name',
            'escape' => true
        ));

        return parent::_prepareColumns();
    }

    /**
     * Grid row URL getter
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getEntityId()));
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
            //$this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
