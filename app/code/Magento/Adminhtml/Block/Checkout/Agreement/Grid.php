<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Adminhtml\Block\Checkout\Agreement;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * @var \Magento\Checkout\Model\Resource\Agreement\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Checkout\Model\Resource\Agreement\CollectionFactory $collectionFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Resource\Agreement\CollectionFactory $collectionFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('agreement_id');
        $this->setId('agreementGrid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $this->setCollection($this->_collectionFactory->create());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('agreement_id',
            array(
                'header'=>__('ID'),
                'index' => 'agreement_id',
                'header_css_class'  => 'col-id',
                'column_css_class'  => 'col-id'
            )
        );

        $this->addColumn('name',
            array(
                'header'=>__('Condition'),
                'index' => 'name',
                'header_css_class'  => 'col-name',
                'column_css_class'  => 'col-name'
            )
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => __('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
                'header_css_class'  => 'col-store-view',
                'column_css_class'  => 'col-store-view'
            ));
        }

        $this->addColumn('is_active', array(
            'header'    => __('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => __('Disabled'),
                1 => __('Enabled')
            ),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/*/edit', array('id' => $row->getId()));
    }

}
