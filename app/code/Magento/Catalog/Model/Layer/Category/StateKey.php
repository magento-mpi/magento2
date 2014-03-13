<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Category;

use Magento\Catalog\Model\Layer\StateKeyInterface;

class StateKey implements StateKeyInterface
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function toString($category)
    {
        return 'STORE_' . $this->storeManager->getStore()->getId()
            . '_CAT_' . $category->getId()
            . '_CUSTGROUP_' . $this->customerSession->getCustomerGroupId();
    }
} 
