<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_SalesRule_Model_Plugin_QuoteConfigProductAttributes
{
    /** @var \Magento_SalesRule_Model_Resource_Rule */
    protected $_ruleResource;

    /** @var \Magento_Customer_Model_Session */
    protected $_customerSession;

    /** @var \Magento_Core_Model_StoreManager */
    protected $_storeManager;

    /**
     * @param Magento_SalesRule_Model_Resource_Rule $ruleResource
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_StoreManager $storeManager
     */
    public function __construct(
        Magento_SalesRule_Model_Resource_Rule $ruleResource,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_StoreManager $storeManager
    ) {
        $this->_ruleResource = $ruleResource;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
    }

    /**
     * Append sales rule product attribute keys to select by quote item collection
     *
     * @param array $attributeKeys
     * @return array
     */
    public function afterGetProductAttributes(array $attributeKeys)
    {
        $attributes = $this->_ruleResource->getActiveAttributes(
            $this->_storeManager->getWebsite()->getId(),
            $this->_customerSession->getCustomer()->getGroupId()
        );

        foreach ($attributes as $attribute) {
            $attributeKeys[] = $attribute['attribute_code'];
        }

        return $attributeKeys;
    }
}
