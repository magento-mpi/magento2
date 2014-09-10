<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Model\Search\Strategy;

/**
 * Wishlist search by email strategy
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Email implements \Magento\MultipleWishlist\Model\Search\Strategy\StrategyInterface
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
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Construct
     *
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Set search fields required by search strategy
     *
     * @param array $params
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setSearchParams(array $params)
    {
        if (empty($params['email']) || !\Zend_Validate::is($params['email'], 'EmailAddress')) {
            throw new \InvalidArgumentException(__('Please input a valid email address.'));
        }
        $this->_email = $params['email'];
    }

    /**
     * Filter given wishlist collection
     *
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection $collection
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function filterCollection(\Magento\Wishlist\Model\Resource\Wishlist\Collection $collection)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_customerFactory->create();
        $customer->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())->loadByEmail($this->_email);

        $collection->filterByCustomerId($customer->getId());
        foreach ($collection as $item) {
            $item->setCustomer($customer);
        }
        return $collection;
    }
}
