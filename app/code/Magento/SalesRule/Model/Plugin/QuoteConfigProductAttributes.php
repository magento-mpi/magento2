<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Plugin;

use Magento\Core\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\SalesRule\Model\Resource\Rule;

class QuoteConfigProductAttributes
{
    /**
     * @var Rule
     */
    protected $_ruleResource;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Rule $ruleResource
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Rule $ruleResource,
        Session $customerSession,
        StoreManagerInterface $storeManager
    ) {
        $this->_ruleResource = $ruleResource;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
    }

    /**
     * Append sales rule product attribute keys to select by quote item collection
     *
     * @param \Magento\Sales\Model\Quote\Config $subject
     * @param array $attributeKeys
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
