<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Block_Catalog_Product_View_Type_Giftcard extends Magento_Catalog_Block_Product_View_Abstract
{
    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        parent::__construct($coreRegistry, $taxData, $catalogData, $coreData, $context, $data);
    }

    public function getAmountSettingsJson($product)
    {
        $result = array('min'=>0, 'max'=>0);
        if ($product->getAllowOpenAmount()) {
            if ($v = $product->getOpenAmountMin()) {
                $result['min'] = $v;
            }
            if ($v = $product->getOpenAmountMax()) {
                $result['max'] = $v;
            }
        }
        return $result;
    }

    public function isConfigured($product)
    {
        if (!$product->getAllowOpenAmount() && !$product->getGiftcardAmounts()) {
            return false;
        }
        return true;
    }

    public function isOpenAmountAvailable($product)
    {
        if (!$product->getAllowOpenAmount()) {
            return false;
        }
        return true;
    }

    public function isAmountAvailable($product)
    {
        if (!$product->getGiftcardAmounts()) {
            return false;
        }
        return true;
    }

    public function getAmounts($product)
    {
        $result = array();
        foreach ($product->getGiftcardAmounts() as $amount) {
            $result[] = $this->_storeManager->getStore()->roundPrice($amount['website_value']);
        }
        sort($result);
        return $result;
    }

    public function getCurrentCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    public function isMessageAvailable($product)
    {
        if ($product->getUseConfigAllowMessage()) {
            return $this->_storeConfig->getConfigFlag(Magento_GiftCard_Model_Giftcard::XML_PATH_ALLOW_MESSAGE);
        } else {
            return (int) $product->getAllowMessage();
        }
    }

    public function isEmailAvailable($product)
    {
        if ($product->getTypeInstance()->isTypePhysical($product)) {
            return false;
        }
        return true;
    }

    public function getCustomerName()
    {
        $firstName = (string)$this->_customerSession->getCustomer()->getFirstname();
        $lastName  = (string)$this->_customerSession->getCustomer()->getLastname();

        if ($firstName && $lastName) {
            return $firstName . ' ' . $lastName;
        } else {
            return '';
        }
    }

    public function getCustomerEmail()
    {
        return (string)$this->_customerSession->getCustomer()->getEmail();
    }

    public function getMessageMaxLength()
    {
        return (int) $this->_storeConfig->getConfig(Magento_GiftCard_Model_Giftcard::XML_PATH_MESSAGE_MAX_LENGTH);
    }

    /**
     * Returns default value to show in input
     *
     * @param string $key
     * @return string
     */
    public function getDefaultValue($key)
    {
        return (string) $this->getProduct()->getPreconfiguredValues()->getData($key);
    }

    /**
     * Returns default sender name to show in input
     *
     * @return string
     */
    public function getDefaultSenderName()
    {
        $senderName = $this->getProduct()->getPreconfiguredValues()->getData('giftcard_sender_name');
        if (!strlen($senderName)) {
            $senderName = $this->getCustomerName();
        }
        return $senderName;
    }

    /**
     * Returns default sender email to show in input
     *
     * @return string
     */
    public function getDefaultSenderEmail()
    {
        $senderEmail = $this->getProduct()->getPreconfiguredValues()->getData('giftcard_sender_email');
        if (!strlen($senderEmail)) {
            $senderEmail = $this->getCustomerEmail();
        }
        return $senderEmail;
    }
}
