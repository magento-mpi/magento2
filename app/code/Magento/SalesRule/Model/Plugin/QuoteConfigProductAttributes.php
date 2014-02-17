<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Plugin;

class QuoteConfigProductAttributes
{
    /** @var \Magento\SalesRule\Model\Resource\Rule */
    protected $_ruleResource;

    /** @var \Magento\Customer\Model\Session */
    protected $_customerSession;

    /** @var \Magento\Core\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * @param \Magento\SalesRule\Model\Resource\Rule $ruleResource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\SalesRule\Model\Resource\Rule $ruleResource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\Model\StoreManagerInterface $storeManager
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
    public function afterGetProductAttributes(\Magento\Sales\Model\Quote\Config $subject, array $attributeKeys)
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
