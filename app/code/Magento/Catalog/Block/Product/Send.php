<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product send to friend block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @module     Catalog
 */
class Magento_Catalog_Block_Product_Send extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Config $catalogConfig
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
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $taxData, $catalogData, $coreData,
            $context, $data);
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->_customerSession->getCustomer()->getName();
    }

    public function getEmail()
    {
        return (string)$this->_customerSession->getCustomer()->getEmail();
    }

    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }

    public function getMaxRecipients()
    {
        $sendToFriendModel = $this->_coreRegistry->registry('send_to_friend_model');
        return $sendToFriendModel->getMaxRecipients();
    }
}
