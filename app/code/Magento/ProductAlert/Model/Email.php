<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ProductAlert Email processor
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_ProductAlert_Model_Email extends Magento_Core_Model_Abstract
{
    const XML_PATH_EMAIL_PRICE_TEMPLATE = 'catalog/productalert/email_price_template';
    const XML_PATH_EMAIL_STOCK_TEMPLATE = 'catalog/productalert/email_stock_template';
    const XML_PATH_EMAIL_IDENTITY       = 'catalog/productalert/email_identity';

    /**
     * Type
     *
     * @var string
     */
    protected $_type = 'price';

    /**
     * Website Model
     *
     * @var Magento_Core_Model_Website
     */
    protected $_website;

    /**
     * Customer model
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Products collection where changed price
     *
     * @var array
     */
    protected $_priceProducts = array();

    /**
     * Product collection which of back in stock
     *
     * @var array
     */
    protected $_stockProducts = array();

    /**
     * Price block
     *
     * @var Magento_ProductAlert_Block_Email_Price
     */
    protected $_priceBlock;

    /**
     * Stock block
     *
     * @var Magento_ProductAlert_Block_Email_Stock
     */
    protected $_stockBlock;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Set model type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * Retrieve model type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set website model
     *
     * @param Magento_Core_Model_Website $website
     * @return Magento_ProductAlert_Model_Email
     */
    public function setWebsite(Magento_Core_Model_Website $website)
    {
        $this->_website = $website;
        return $this;
    }

    /**
     * Set website id
     *
     * @param int $websiteId
     * @return Magento_ProductAlert_Model_Email
     */
    public function setWebsiteId($websiteId)
    {
        $this->_website = Mage::app()->getWebsite($websiteId);
        return $this;
    }

    /**
     * Set customer by id
     *
     * @param int $customerId
     * @return Magento_ProductAlert_Model_Email
     */
    public function setCustomerId($customerId)
    {
        $this->_customer = Mage::getModel('Magento_Customer_Model_Customer')->load($customerId);
        return $this;
    }

    /**
     * Set customer model
     *
     * @param Magento_Customer_Model_Customer $customer
     * @return Magento_ProductAlert_Model_Email
     */
    public function setCustomer(Magento_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Clean data
     *
     * @return Magento_ProductAlert_Model_Email
     */
    public function clean()
    {
        $this->_customer      = null;
        $this->_priceProducts = array();
        $this->_stockProducts = array();

        return $this;
    }

    /**
     * Add product (price change) to collection
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_ProductAlert_Model_Email
     */
    public function addPriceProduct(Magento_Catalog_Model_Product $product)
    {
        $this->_priceProducts[$product->getId()] = $product;
        return $this;
    }

    /**
     * Add product (back in stock) to collection
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_ProductAlert_Model_Email
     */
    public function addStockProduct(Magento_Catalog_Model_Product $product)
    {
        $this->_stockProducts[$product->getId()] = $product;
        return $this;
    }

    /**
     * Retrieve price block
     *
     * @return Magento_ProductAlert_Block_Email_Price
     */
    protected function _getPriceBlock()
    {
        if (is_null($this->_priceBlock)) {
            $this->_priceBlock = Mage::helper('Magento_ProductAlert_Helper_Data')
                ->createBlock('Magento_ProductAlert_Block_Email_Price');
        }
        return $this->_priceBlock;
    }

    /**
     * Retrieve stock block
     *
     * @return Magento_ProductAlert_Block_Email_Stock
     */
    protected function _getStockBlock()
    {
        if (is_null($this->_stockBlock)) {
            $this->_stockBlock = Mage::helper('Magento_ProductAlert_Helper_Data')
                ->createBlock('Magento_ProductAlert_Block_Email_Stock');
        }
        return $this->_stockBlock;
    }

    /**
     * Send customer email
     *
     * @return bool
     */
    public function send()
    {
        if (is_null($this->_website) || is_null($this->_customer)) {
            return false;
        }
        if (($this->_type == 'price' && count($this->_priceProducts) == 0)
            || ($this->_type == 'stock' && count($this->_stockProducts) == 0)
        ) {
            return false;
        }
        if (!$this->_website->getDefaultGroup() || !$this->_website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }

        $store      = $this->_website->getDefaultStore();
        $storeId    = $store->getId();

        if ($this->_type == 'price' && !$this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE, $storeId)) {
            return false;
        } elseif ($this->_type == 'stock' && !$this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_STOCK_TEMPLATE, $storeId)) {
            return false;
        }

        if ($this->_type != 'price' && $this->_type != 'stock') {
            return false;
        }

        $appEmulation = Mage::getSingleton('Magento_Core_Model_App_Emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        if ($this->_type == 'price') {
            $this->_getPriceBlock()
                ->setStore($store)
                ->reset();
            foreach ($this->_priceProducts as $product) {
                $product->setCustomerGroupId($this->_customer->getGroupId());
                $this->_getPriceBlock()->addProduct($product);
            }
            $block = $this->_getPriceBlock()->toHtml();
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE, $storeId);
        } else {
            $this->_getStockBlock()
                ->setStore($store)
                ->reset();
            foreach ($this->_stockProducts as $product) {
                $product->setCustomerGroupId($this->_customer->getGroupId());
                $this->_getStockBlock()->addProduct($product);
            }
            $block = $this->_getStockBlock()->toHtml();
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_STOCK_TEMPLATE, $storeId);
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        Mage::getModel('Magento_Core_Model_Email_Template')
            ->setDesignConfig(array(
                'area'  => Magento_Core_Model_App_Area::AREA_FRONTEND,
                'store' => $storeId
            ))->sendTransactional(
                $templateId,
                $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId),
                $this->_customer->getEmail(),
                $this->_customer->getName(),
                array(
                    'customerName'  => $this->_customer->getName(),
                    'alertGrid'     => $block
                )
            );

        return true;
    }
}
