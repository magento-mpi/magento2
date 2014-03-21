<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Model;

/**
 * ProductAlert observer
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Observer
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
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\ProductAlert\Model\Resource\Price\CollectionFactory
     */
    protected $_priceColFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Stdlib\DateTime\DateTimeFactory
     */
    protected $_dateFactory;

    /**
     * @var \Magento\ProductAlert\Model\Resource\Stock\CollectionFactory
     */
    protected $_stockColFactory;

    /**
     * @var \Magento\TranslateInterface
     */
    protected $_translate;

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\ProductAlert\Model\EmailFactory
     */
    protected $_emailFactory;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\ProductAlert\Model\Resource\Price\CollectionFactory $priceColFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Stdlib\DateTime\DateTimeFactory $dateFactory
     * @param \Magento\ProductAlert\Model\Resource\Stock\CollectionFactory $stockColFactory
     * @param \Magento\TranslateInterface $translate
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\ProductAlert\Model\EmailFactory $emailFactory
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ProductAlert\Model\Resource\Price\CollectionFactory $priceColFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\ProductAlert\Model\Resource\Stock\CollectionFactory $stockColFactory,
        \Magento\TranslateInterface $translate,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\ProductAlert\Model\EmailFactory $emailFactory
    ) {
        $this->_taxData = $taxData;
        $this->_storeConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
        $this->_priceColFactory = $priceColFactory;
        $this->_customerFactory = $customerFactory;
        $this->_productFactory = $productFactory;
        $this->_dateFactory = $dateFactory;
        $this->_stockColFactory = $stockColFactory;
        $this->_translate = $translate;
        $this->_transportBuilder = $transportBuilder;
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
            catch (\Exception $e) {
                $this->_errors[] = $e->getMessage();
            }
        }
        return $this->_websites;
    }

    /**
     * Process price emails
     *
     * @param \Magento\ProductAlert\Model\Email $email
     * @return $this
     */
    protected function _processPrice(\Magento\ProductAlert\Model\Email $email)
    {
        $email->setType('price');
        foreach ($this->_getWebsites() as $website) {
            /* @var $website \Magento\Store\Model\Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            if (!$this->_storeConfig->getValue(
                self::XML_PATH_PRICE_ALLOW, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }
            try {
                $collection = $this->_priceColFactory->create()
                    ->addWebsiteFilter($website->getId())
                    ->setCustomerOrder();
            }
            catch (\Exception $e) {
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
                catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
            if ($previousCustomer) {
                try {
                    $email->send();
                }
                catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }
        return $this;
    }

    /**
     * Process stock emails
     *
     * @param \Magento\ProductAlert\Model\Email $email
     * @return $this
     */
    protected function _processStock(\Magento\ProductAlert\Model\Email $email)
    {
        $email->setType('stock');

        foreach ($this->_getWebsites() as $website) {
            /* @var $website \Magento\Store\Model\Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            if (!$this->_storeConfig->getValue(
                self::XML_PATH_STOCK_ALLOW, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE,
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
            catch (\Exception $e) {
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
                    /* @var $product \Magento\Catalog\Model\Product */
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
                catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            if ($previousCustomer) {
                try {
                    $email->send();
                }
                catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }

        return $this;
    }

    /**
     * Send email to administrator if error
     *
     * @return $this
     */
    protected function _sendErrorEmail()
    {
        if (count($this->_errors)) {
            if (!$this->_storeConfig->getValue(self::XML_PATH_ERROR_TEMPLATE, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {
                return $this;
            }

            $this->_translate->setTranslateInline(false);

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->_storeConfig->getValue(self::XML_PATH_ERROR_TEMPLATE, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE))
                ->setTemplateOptions(array(
                    'area'  => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId()
                ))
                ->setTemplateVars(array('warnings' => join("\n", $this->_errors)))
                ->setFrom($this->_storeConfig->getValue(self::XML_PATH_ERROR_IDENTITY, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE))
                ->addTo($this->_storeConfig->getValue(self::XML_PATH_ERROR_RECIPIENT, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE))
                ->getTransport();

            $transport->sendMessage();

            $this->_translate->setTranslateInline(true);
            $this->_errors[] = array();
        }
        return $this;
    }

    /**
     * Run process send product alerts
     *
     * @return $this
     */
    public function process()
    {
        /* @var $email \Magento\ProductAlert\Model\Email */
        $email = $this->_emailFactory->create();
        $this->_processPrice($email);
        $this->_processStock($email);
        $this->_sendErrorEmail();

        return $this;
    }
}
