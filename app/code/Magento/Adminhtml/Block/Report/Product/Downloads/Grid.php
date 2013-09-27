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
 * Adminhtml product downloads report grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Product_Downloads_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magento_Reports_Model_Resource_Product_Downloads_CollectionFactory
     */
    protected $_downloadsFactory;

    /**
     * @param Magento_Reports_Model_Resource_Product_Downloads_CollectionFactory $downloadsFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Reports_Model_Resource_Product_Downloads_CollectionFactory $downloadsFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_downloadsFactory = $downloadsFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('downloadsGrid');
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('store')) {
            $storeId = (int)$this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }

        $collection = $this->_downloadsFactory->create()
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addAttributeToFilter('type_id', array(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE))
            ->addSummary();

        if ($storeId) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => __('Product'),
            'index'     => 'name',
            'header_css_class'  => 'col-product',
            'column_css_class'  => 'col-product'
        ));

        $this->addColumn('link_title', array(
            'header'    => __('Link'),
            'index'     => 'link_title',
            'header_css_class'  => 'col-link',
            'column_css_class'  => 'col-link'
        ));

        $this->addColumn('sku', array(
            'header'    =>__('SKU'),
            'index'     =>'sku',
            'header_css_class'  => 'col-sku',
            'column_css_class'  => 'col-sku'
        ));

        $this->addColumn('purchases', array(
            'header'    => __('Purchases'),
            'width'     => '215px',
            'align'     => 'right',
            'filter'    => false,
            'index'     => 'purchases',
            'type'      => 'number',
            'renderer'  => 'Magento_Adminhtml_Block_Report_Product_Downloads_Renderer_Purchases',
            'header_css_class'  => 'col-purchases',
            'column_css_class'  => 'col-purchases'
        ));

        $this->addColumn('downloads', array(
            'header'    => __('Downloads'),
            'width'     => '215px',
            'align'     => 'right',
            'filter'    => false,
            'index'     => 'downloads',
            'type'      => 'number',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        $this->addExportType('*/*/exportDownloadsCsv', __('CSV'));
        $this->addExportType('*/*/exportDownloadsExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }
}
