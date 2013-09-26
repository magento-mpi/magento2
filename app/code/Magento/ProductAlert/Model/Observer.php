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
 * ProductAlert observer
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_ProductAlert_Model_Observer
{
    /**
     * Error email template configuration
     */
    const XML_PATH_ERROR_TEMPLATE   = 'catalog/productalert_cron/error_email_template';

    /**
     * Error email identity configuration
     */
    const XML_PATH_ERROR_IDENTITY   = 'catalog/productalert_cron/error_email_identity';

    /**
     * 'Send error emails to' configuration
     */
    const XML_PATH_ERROR_RECIPIENT  = 'catalog/productalert_cron/error_email';

    /**
     * Allow price alert
     *
     */
    const XML_PATH_PRICE_ALLOW      = 'catalog/productalert/allow_price';

    /**
     * Allow stock alert
     *
     */
    const XML_PATH_STOCK_ALLOW      = 'catalog/productalert/allow_stock';

    /**
     * Website collection array
     *
     * @var array
     */
    protected $_websites;

    /**
     * Warning (exception) errors array
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_ProductAlert_Model_Resource_Price_CollectionFactory
     */
    protected $_priceColFactory;

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Core_Model_DateFactory
     */
    protected $_dateFactory;

    /**
     * @var Magento_ProductAlert_Model_Resource_Stock_CollectionFactory
     */
    protected $_stockColFactory;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translate;

    /**
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var Magento_ProductAlert_Model_EmailFactory
     */
    protected $_emailFactory;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_ProductAlert_Model_Resource_Price_CollectionFactory $priceColFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Model_DateFactory $dateFactory
     * @param Magento_ProductAlert_Model_Resource_Stock_CollectionFactory $stockColFactory
     * @param Magento_Core_Model_Translate $translate
     * @param Magento_Core_Model_Email_TemplateFactory $templateFactory
     * @param Magento_ProductAlert_Model_EmailFactory $emailFactory
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_ProductAlert_Model_Resource_Price_CollectionFactory $priceColFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Model_DateFactory $dateFactory,
        Magento_ProductAlert_Model_Resource_Stock_CollectionFactory $stockColFactory,
        Magento_Core_Model_Translate $translate,
        Magento_Core_Model_Email_TemplateFactory $templateFactory,
        Magento_ProductAlert_Model_EmailFactory $emailFactory
    ) {
        $this->_taxData = $taxData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
        $this->_priceColFactory = $priceColFactory;
        $this->_customerFactory = $customerFactory;
        $this->_productFactory = $productFactory;
        $this->_dateFactory = $dateFactory;
        $this->_stockColFactory = $stockColFactory;
        $this->_translate = $translate;
        $this->_templateFactory = $templateFactory;
        $this->_emailFactory = $emailFactory;
    }

    /**
     * Retrieve website collection array
     *
     * @return array
     */
    protected function _getWebsites()
    {
        if (is_null($this->_websites)) {
            try {
                $this->_websites = $this->_storeManager->getWebsites();
            }
            catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
            }
        }
        return $this->_websites;
    }

    /**
     * Process price emails
     *
     * @param Magento_ProductAlert_Model_Email $email
     * @return Magento_ProductAlert_Model_Observer
     */
    protected function _processPrice(Magento_ProductAlert_Model_Email $email)
    {
        $email->setType('price');
        foreach ($this->_getWebsites() as $website) {
            /* @var $website Magento_Core_Model_Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            if (!$this->_coreStoreConfig->getConfig(
                self::XML_PATH_PRICE_ALLOW,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }
            try {
                $collection = $this->_priceColFactory->create()
                    ->addWebsiteFilter($website->getId())
                    ->setCustomerOrder();
            }
            catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                try {
                    if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                        $customer = $this->_customerFactory->create()->load($alert->getCustomerId());
                        if ($previousCustomer) {
                            $email->send();
                        }
                        if (!$customer) {
                            continue;
                        }
                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomer($customer);
                    } else {
                        $customer = $previousCustomer;
                    }

                    $product = $this->_productFactory->create()
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());
                    if (!$product) {
                        continue;
                    }
                    $product->setCustomerGroupId($customer->getGroupId());
                    if ($alert->getPrice() > $product->getFinalPrice()) {
                        $productPrice = $product->getFinalPrice();
                        $product->setFinalPrice($this->_taxData->getPrice($product, $productPrice));
                        $product->setPrice($this->_taxData->getPrice($product, $product->getPrice()));
                        $email->addPriceProduct($product);

                        $alert->setPrice($productPrice);
                        $alert->setLastSendDate($this->_dateFactory->create()->gmtDate());
                        $alert->setSendCount($alert->getSendCount() + 1);
                        $alert->setStatus(1);
                        $alert->save();
                    }
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
            if ($previousCustomer) {
                try {
                    $email->send();
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }
        return $this;
    }

    /**
     * Process stock emails
     *
     * @param Magento_ProductAlert_Model_Email $email
     * @return Magento_ProductAlert_Model_Observer
     */
    protected function _processStock(Magento_ProductAlert_Model_Email $email)
    {
        $email->setType('stock');

        foreach ($this->_getWebsites() as $website) {
            /* @var $website Magento_Core_Model_Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            if (!$this->_coreStoreConfig->getConfig(
                self::XML_PATH_STOCK_ALLOW,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }
            try {
                $collection = $this->_stockColFactory->create()
                    ->addWebsiteFilter($website->getId())
                    ->addStatusFilter(0)
                    ->setCustomerOrder();
            }
            catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                try {
                    if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                        $customer = $this->_customerFactory->create()->load($alert->getCustomerId());
                        if ($previousCustomer) {
                            $email->send();
                        }
                        if (!$customer) {
                            continue;
                        }
                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomer($customer);
                    } else {
                        $customer = $previousCustomer;
                    }

                    $product = $this->_productFactory->create()
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());
                    /* @var $product Magento_Catalog_Model_Product */
                    if (!$product) {
                        continue;
                    }

                    $product->setCustomerGroupId($customer->getGroupId());

                    if ($product->isSalable()) {
                        $email->addStockProduct($product);

                        $alert->setSendDate($this->_dateFactory->create()->gmtDate());
                        $alert->setSendCount($alert->getSendCount() + 1);
                        $alert->setStatus(1);
                        $alert->save();
                    }
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            if ($previousCustomer) {
                try {
                    $email->send();
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }

        return $this;
    }

    /**
     * Send email to administrator if error
     *
     * @return Magento_ProductAlert_Model_Observer
     */
    protected function _sendErrorEmail()
    {
        if (count($this->_errors)) {
            if (!$this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_TEMPLATE)) {
                return $this;
            }

            $$this->_translate->setTranslateInline(false);

            /* @var $emailTemplate Magento_Core_Model_Email_Template */
            $this->_templateFactory->create()->setDesignConfig(array('area'  => 'backend'))
                ->sendTransactional(
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_TEMPLATE),
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_IDENTITY),
                    $this->_coreStoreConfig->getConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings' => join("\n", $this->_errors))
                );

            $$this->_translate->setTranslateInline(true);
            $this->_errors[] = array();
        }
        return $this;
    }

    /**
     * Run process send product alerts
     *
     * @return Magento_ProductAlert_Model_Observer
     */
    public function process()
    {
        /* @var $email Magento_ProductAlert_Model_Email */
        $email = $this->_emailFactory->create();
        $this->_processPrice($email);
        $this->_processStock($email);
        $this->_sendErrorEmail();

        return $this;
    }
}
