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
namespace Magento\ProductAlert\Model;

class Email extends \Magento\Core\Model\AbstractModel
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
     * @var \Magento\Core\Model\Website
     */
    protected $_website;

    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\Customer
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
     * @var \Magento\ProductAlert\Block\Email\Price
     */
    protected $_priceBlock;

    /**
     * Stock block
     *
     * @var \Magento\ProductAlert\Block\Email\Stock
     */
    protected $_stockBlock;

    /**
     * Product alert data
     *
     * @var \Magento\ProductAlert\Helper\Data
     */
    protected $_productAlertData = null;

    /**
     * @param \Magento\ProductAlert\Helper\Data $productAlertData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\ProductAlert\Helper\Data $productAlertData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productAlertData = $productAlertData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
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
     * @param \Magento\Core\Model\Website $website
     * @return \Magento\ProductAlert\Model\Email
     */
    public function setWebsite(\Magento\Core\Model\Website $website)
    {
        $this->_website = $website;
        return $this;
    }

    /**
     * Set website id
     *
     * @param int $websiteId
     * @return \Magento\ProductAlert\Model\Email
     */
    public function setWebsiteId($websiteId)
    {
        $this->_website = \Mage::app()->getWebsite($websiteId);
        return $this;
    }

    /**
     * Set customer by id
     *
     * @param int $customerId
     * @return \Magento\ProductAlert\Model\Email
     */
    public function setCustomerId($customerId)
    {
        $this->_customer = \Mage::getModel('Magento\Customer\Model\Customer')->load($customerId);
        return $this;
    }

    /**
     * Set customer model
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Magento\ProductAlert\Model\Email
     */
    public function setCustomer(\Magento\Customer\Model\Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Clean data
     *
     * @return \Magento\ProductAlert\Model\Email
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
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\ProductAlert\Model\Email
     */
    public function addPriceProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->_priceProducts[$product->getId()] = $product;
        return $this;
    }

    /**
     * Add product (back in stock) to collection
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\ProductAlert\Model\Email
     */
    public function addStockProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->_stockProducts[$product->getId()] = $product;
        return $this;
    }

    /**
     * Retrieve price block
     *
     * @return \Magento\ProductAlert\Block\Email\Price
     */
    protected function _getPriceBlock()
    {
        if (is_null($this->_priceBlock)) {
            $this->_priceBlock = $this->_productAlertData
                ->createBlock('Magento\ProductAlert\Block\Email\Price');
        }
        return $this->_priceBlock;
    }

    /**
     * Retrieve stock block
     *
     * @return \Magento\ProductAlert\Block\Email\Stock
     */
    protected function _getStockBlock()
    {
        if (is_null($this->_stockBlock)) {
            $this->_stockBlock = $this->_productAlertData
                ->createBlock('Magento\ProductAlert\Block\Email\Stock');
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

        if ($this->_type == 'price' && !\Mage::getStoreConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE, $storeId)) {
            return false;
        } elseif ($this->_type == 'stock' && !\Mage::getStoreConfig(self::XML_PATH_EMAIL_STOCK_TEMPLATE, $storeId)) {
            return false;
        }

        if ($this->_type != 'price' && $this->_type != 'stock') {
            return false;
        }

        $appEmulation = \Mage::getSingleton('Magento\Core\Model\App\Emulation');
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
            $templateId = \Mage::getStoreConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE, $storeId);
        } else {
            $this->_getStockBlock()
                ->setStore($store)
                ->reset();
            foreach ($this->_stockProducts as $product) {
                $product->setCustomerGroupId($this->_customer->getGroupId());
                $this->_getStockBlock()->addProduct($product);
            }
            $block = $this->_getStockBlock()->toHtml();
            $templateId = \Mage::getStoreConfig(self::XML_PATH_EMAIL_STOCK_TEMPLATE, $storeId);
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        \Mage::getModel('Magento\Core\Model\Email\Template')
            ->setDesignConfig(array(
                'area'  => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ))->sendTransactional(
                $templateId,
                \Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId),
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
