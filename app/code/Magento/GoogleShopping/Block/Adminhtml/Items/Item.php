<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Shopping Items
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Block_Adminhtml_Items_Item extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * Collection factory
     *
     * @var Magento_GoogleShopping_Model_Resource_Item_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_GoogleShopping_Model_Resource_Item_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_GoogleShopping_Model_Resource_Item_CollectionFactory $collectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('items');
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Magento_GoogleShopping_Block_Adminhtml_Items_Item
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $store = $this->_storeManager->getStore($this->getRequest()->getParam('store'));
        $collection->addStoreFilter($store->getId());
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_GoogleShopping_Block_Adminhtml_Items_Item
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name',
            array(
                'header'    => __('Product'),
                'width'     => '30%',
                'index'     => 'name',
        ));

        $this->addColumn('expires',
            array(
                'header'    => __('Expires'),
                'type'      => 'datetime',
                'width'     => '100px',
                'index'     => 'expires',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid massaction actions
     *
     * @return Magento_GoogleShopping_Block_Adminhtml_Items_Item
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('item');
        $this->setNoFilterMassactionColumn(true);

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => __('Delete'),
             'url'      => $this->getUrl('*/*/massDelete', array('_current'=>true)),
             'confirm'  => __('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('refresh', array(
             'label'    => __('Synchronize'),
             'url'      => $this->getUrl('*/*/refresh', array('_current'=>true)),
             'confirm'  => __('This action will update items\' attributes and remove items that are not available in Google Content. If an attribute was deleted from the mapping, it will also be deleted from Google. Do you want to continue?')
        ));
        return $this;
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
