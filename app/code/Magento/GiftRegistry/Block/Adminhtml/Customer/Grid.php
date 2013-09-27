<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Customer_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magento_GiftRegistry_Model_EntityFactory
     */
    protected $entityFactory;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $systemStore;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_GiftRegistry_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_System_Store $systemStore
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_GiftRegistry_Model_EntityFactory $entityFactory,
        Magento_Core_Model_System_Store $systemStore,
        array $data = array()
    ) {
        $this->entityFactory = $entityFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);

        $this->systemStore = $systemStore;
    }

    /**
     * Set default sort
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('registry_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Customer_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Magento_GiftRegistry_Model_Resource_Entity_Collection */
        $collection = $this->entityFactory->create()->getCollection();
        $collection->filterByCustomerId($this->getRequest()->getParam('id'));
        $collection->addRegistryInfo();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for grid
     *
     * @return Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Customer_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header' => __('Event'),
            'index'  => 'title'
        ));

        $this->addColumn('registrants', array(
            'header' => __('Recipients'),
            'index'  => 'registrants'
        ));

        $this->addColumn('event_date', array(
            'header'  => __('Event Date'),
            'index'   => 'event_date',
            'type'    => 'date',
            'default' => '--'
        ));

        $this->addColumn('qty', array(
            'header' => __('Total Items'),
            'index'  => 'qty',
            'type'   => 'number'
        ));

        $this->addColumn('qty_fulfilled', array(
            'header' => __('Fulfilled'),
            'index'  => 'qty_fulfilled',
            'type'   => 'number',
        ));

        $this->addColumn('qty_remaining', array(
            'header' => __('Remaining'),
            'index'  => 'qty_remaining',
            'type'   => 'number'
        ));

        $this->addColumn('is_public', array(
            'header'  => __('Public'),
            'index'   => 'is_public',
            'type'    => 'options',
            'options' => array(
                '0' => __('No'),
                '1' => __('Yes'),
            )
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => __('Website'),
                'index'  => 'website_id',
                'type'   => 'options',
                'options' => $this->systemStore->getWebsiteOptionHash()
            ));
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'       => $row->getId(),
            'customer' => $row->getCustomerId()
        ));
    }

    /**
     * Retrieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
