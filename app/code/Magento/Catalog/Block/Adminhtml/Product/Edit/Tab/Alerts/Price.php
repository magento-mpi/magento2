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
 * Sign up for an alert when the product price changes grid
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts;

use Magento\Backend\Block\Widget\Grid;
use \Magento\Backend\Block\Widget\Grid\Extended;

class Price extends Extended
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * @var \Magento\ProductAlert\Model\PriceFactory
     */
    protected $_priceFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\ProductAlert\Model\PriceFactory $priceFactory
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\ProductAlert\Model\PriceFactory $priceFactory,
        \Magento\Catalog\Helper\Data $catalogData,
        array $data = array()
    ) {
        $this->_priceFactory = $priceFactory;
        $this->_catalogData = $catalogData;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('alertPrice');
        $this->setDefaultSort('add_date');
        $this->setDefaultSort('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setEmptyText(__('There are no customers for this alert.'));
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $productId = $this->getRequest()->getParam('id');
        $websiteId = 0;
        if ($store = $this->getRequest()->getParam('store')) {
            $websiteId = $this->_storeManager->getStore($store)->getWebsiteId();
        }
        if ($this->_catalogData->isModuleEnabled('Magento_ProductAlert')) {
            $collection = $this->_priceFactory->create()->getCustomerCollection()
                ->join($productId, $websiteId);
            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header'    => __('First Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => __('Last Name'),
            'index'     => 'lastname',
        ));

        $this->addColumn('email', array(
            'header'    => __('Email'),
            'index'     => 'email',
        ));

        $this->addColumn('price', array(
            'header'    => __('Price'),
            'index'     => 'price',
            'type'      => 'currency',
            'currency_code'
                        => $this->_storeConfig->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE)
        ));

        $this->addColumn('add_date', array(
            'header'    => __('Subscribe Date'),
            'index'     => 'add_date',
            'type'      => 'date'
        ));

        $this->addColumn('last_send_date', array(
            'header'    => __('Last Notified'),
            'index'     => 'last_send_date',
            'type'      => 'date'
        ));

        $this->addColumn('send_count', array(
            'header'    => __('Send Count'),
            'index'     => 'send_count',
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        $productId = $this->getRequest()->getParam('id');
        $storeId   = $this->getRequest()->getParam('store', 0);
        if ($storeId) {
            $storeId = $this->_storeManager->getStore($storeId)->getId();
        }
        return $this->getUrl('catalog/product/alertsPriceGrid', array(
            'id'    => $productId,
            'store' => $storeId
        ));
    }
}
