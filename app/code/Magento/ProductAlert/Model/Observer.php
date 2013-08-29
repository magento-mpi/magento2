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
     * @param Magento_Tax_Helper_Data $taxData
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData
    ) {
        $this->_taxData = $taxData;
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
                $this->_websites = Mage::app()->getWebsites();
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
            if (!Mage::getStoreConfig(
                self::XML_PATH_PRICE_ALLOW,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }
            try {
                $collection = Mage::getModel('Magento_ProductAlert_Model_Price')
                    ->getCollection()
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
                        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($alert->getCustomerId());
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

                    $product = Mage::getModel('Magento_Catalog_Model_Product')
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
                        $alert->setLastSendDate(Mage::getModel('Magento_Core_Model_Date')->gmtDate());
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
            if (!Mage::getStoreConfig(
                self::XML_PATH_STOCK_ALLOW,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }
            try {
                $collection = Mage::getModel('Magento_ProductAlert_Model_Stock')
                    ->getCollection()
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
                        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($alert->getCustomerId());
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

                    $product = Mage::getModel('Magento_Catalog_Model_Product')
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());
                    /* @var $product Magento_Catalog_Model_Product */
                    if (!$product) {
                        continue;
                    }

                    $product->setCustomerGroupId($customer->getGroupId());

                    if ($product->isSalable()) {
                        $email->addStockProduct($product);

                        $alert->setSendDate(Mage::getModel('Magento_Core_Model_Date')->gmtDate());
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
            if (!Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE)) {
                return $this;
            }

            $translate = Mage::getSingleton('Magento_Core_Model_Translate');
            /* @var $translate Magento_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('Magento_Core_Model_Email_Template');
            /* @var $emailTemplate Magento_Core_Model_Email_Template */
            $emailTemplate->setDesignConfig(array('area'  => 'backend'))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings' => join("\n", $this->_errors))
                );

            $translate->setTranslateInline(true);
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
        $email = Mage::getModel('Magento_ProductAlert_Model_Email');
        /* @var $email Magento_ProductAlert_Model_Email */
        $this->_processPrice($email);
        $this->_processStock($email);
        $this->_sendErrorEmail();

        return $this;
    }
}
