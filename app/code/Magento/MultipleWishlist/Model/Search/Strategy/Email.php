<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist search by email strategy
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Model_Search_Strategy_Email implements Magento_MultipleWishlist_Model_Search_Strategy_Interface
{
    /**
     * Email provided for search
     *
     * @var string
     */
    protected $_email;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer factory
     *
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Construct
     *
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Set search fields required by search strategy
     *
     * @param array $params
     * @throws InvalidArgumentException
     */
    public function setSearchParams(array $params)
    {
        if (empty($params['email']) || !Zend_Validate::is($params['email'], 'EmailAddress')) {
            throw new InvalidArgumentException(__('Please input a valid email address.'));
        }
        $this->_email = $params['email'];
    }

    /**
     * Filter given wishlist collection
     *
     * @param Magento_Wishlist_Model_Resource_Wishlist_Collection $collection
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function filterCollection(Magento_Wishlist_Model_Resource_Wishlist_Collection $collection)
    {
        /** @var Magento_Customer_Model_Customer $customer */
        $customer = $this->_customerFactory->create();
        $customer->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())
            ->loadByEmail($this->_email);

        $collection->filterByCustomer($customer);
        foreach ($collection as $item){
            $item->setCustomer($customer);
        }
        return $collection;
    }
}
