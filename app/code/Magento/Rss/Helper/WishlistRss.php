<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Helper;

class WishlistRss extends \Magento\Wishlist\Helper\Data
{
    /**
     * @var \Magento\Customer\Service\V1\Data\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder
     */
    protected $_customerBuilder;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\PostData $postDataHelper
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Registry $coreRegistry,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\PostData $postDataHelper,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
    ) {
        $this->_customerViewHelper = $customerViewHelper;
        $this->_customerBuilder = $customerBuilder;
        $this->_customerAccountService = $customerAccountService;

        parent::__construct(
            $context,
            $coreData,
            $coreRegistry,
            $coreStoreConfig,
            $customerSession,
            $wishlistFactory,
            $storeManager,
            $postDataHelper
        );
    }

    /**
     * Retrieve Wishlist model
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = $this->_wishlistFactory->create();

            $wishlistId = $this->_getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
            } else {
                if ($this->getCustomer()->getId()) {
                    $this->_wishlist->loadByCustomerId($this->getCustomer()->getId());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve Customer instance
     *
     * @return \Magento\Customer\Service\V1\Data\Customer
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = $this->_customerBuilder->create();

            $params = $this->_coreData->urlDecode($this->_getRequest()->getParam('data'));
            $data   = explode(',', $params);
            $cId    = abs(intval($data[0]));
            if ($cId && ($cId == $this->_customerSession->getCustomerId())) {
                $this->_customer = $this->_customerSession->getCustomerDataObject();
            }
        }

        return $this->_customer;
    }

    /**
     * Set current customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return void
     */
    public function setCustomer(\Magento\Customer\Model\Customer $customer)
    {
        /* TODO this method must be eliminated after refactoring of Magento\Wishlist\Helper\Data */
        $this->_customer = $this->_customerAccountService->getCustomer($customer->getId());
    }

    /**
     * Retrieve customer name
     *
     * @return string|void
     */
    public function getCustomerName()
    {
        /* TODO this method must be eliminated after refactoring of Magento\Wishlist\Helper\Data */
        return $this->getCustomer()
            ? $this->_customerViewHelper->getCustomerName($this->getCustomer())
            : null;
    }
}
