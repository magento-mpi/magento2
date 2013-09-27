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
 * Adminhtml dashboard most viewed products grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Dashboard_Tab_Products_Viewed extends Magento_Adminhtml_Block_Dashboard_Grid
{
    /**
     * @var Magento_Reports_Model_Resource_Product_CollectionFactory
     */
    protected $_productsFactory;

    /**
     * @param Magento_Reports_Model_Resource_Product_CollectionFactory $productsFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Reports_Model_Resource_Product_CollectionFactory $productsFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_productsFactory = $productsFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsReviewedGrid');
    }

    protected function _prepareCollection()
    {
        if ($this->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else {
            $storeId = (int)$this->getParam('store');
        }
        $collection = $this->_productsFactory->create()
            ->addAttributeToSelect('*')
            ->addViewsCount()
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>__('Product'),
            'sortable'  => false,
            'index'     =>'name'
        ));

        $this->addColumn('price', array(
            'header'    =>__('Price'),
            'width'     =>'120px',
            'type'      =>'currency',
            'currency_code' => (string) $this->_storeManager->getStore((int)$this->getParam('store'))
                ->getBaseCurrencyCode(),
            'sortable'  => false,
            'index'     =>'price'
        ));

        $this->addColumn('views', array(
            'header'    =>__('Views'),
            'width'     =>'120px',
            'align'     =>'right',
            'sortable'  => false,
            'index'     =>'views'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        $params = array('id'=>$row->getId());
        if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }
        return $this->getUrl('*/catalog_product/edit', $params);
    }
}
