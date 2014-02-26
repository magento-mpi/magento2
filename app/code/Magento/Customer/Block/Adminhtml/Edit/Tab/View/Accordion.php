<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Service\V1\Dto\Customer;
use Magento\Exception\NoSuchEntityException;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Adminhtml customer recent orders grid block
 */
class Accordion extends \Magento\Backend\Block\Widget\Accordion
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Wishlist\Model\Resource\Item\CollectionFactory
     */
    protected $_itemsFactory;

    /** @var \Magento\Customer\Model\Config\Share  */
    protected $_shareConfig;

    /** @var \Magento\Customer\Service\V1\CustomerServiceInterface  */
    protected $_customerService;

    /** @var \Magento\Customer\Service\V1\Dto\CustomerBuilder  */
    protected $_customerBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Wishlist\Model\Resource\Item\CollectionFactory $itemsFactory
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Customer\Model\Config\Share $shareConfig
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Wishlist\Model\Resource\Item\CollectionFactory $itemsFactory,
        \Magento\Core\Model\Registry $registry,
        \Magento\Customer\Model\Config\Share $shareConfig,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_quoteFactory = $quoteFactory;
        $this->_itemsFactory = $itemsFactory;
        $this->_shareConfig = $shareConfig;
        $this->_customerService = $customerService;
        $this->_customerBuilder = $customerBuilder;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->setId('customerViewAccordion');

        $this->addItem('lastOrders', array(
            'title'       => __('Recent Orders'),
            'ajax'        => true,
            'content_url' => $this->getUrl('customer/*/lastOrders', array('_current' => true)),
        ));

        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);    
        $customer = $this->getCustomer($customerId);
        $websiteIds = $this->_shareConfig->getSharedWebsiteIds($customer->getWebsiteId());
        // add shopping cart block of each website
        foreach ($websiteIds as $websiteId) {
            $website = $this->_storeManager->getWebsite($websiteId);

            // count cart items
            $cartItemsCount = $this->_quoteFactory->create()
                ->setWebsite($website)->loadByCustomer($customerId)
                ->getItemsCollection(false)
                ->addFieldToFilter('parent_item_id', array('null' => true))
                ->getSize();
            // prepare title for cart
            $title = __('Shopping Cart - %1 item(s)', $cartItemsCount);
            if (count($websiteIds) > 1) {
                $title = __('Shopping Cart of %1 - %2 item(s)', $website->getName(), $cartItemsCount);
            }

            // add cart ajax accordion
            $this->addItem('shopingCart' . $websiteId, array(
                'title'   => $title,
                'ajax'    => true,
                'content_url' => $this->getUrl('customer/*/viewCart',
                        array('_current' => true, 'website_id' => $websiteId)),
            ));
        }

        // count wishlist items
        $wishlistCount = $this->_itemsFactory->create()
            ->addCustomerIdFilter($customerId)
            ->addStoreData()
            ->getSize();
        // add wishlist ajax accordion
        $this->addItem('wishlist', array(
            'title' => __('Wishlist - %1 item(s)', $wishlistCount),
            'ajax'  => true,
            'content_url' => $this->getUrl('customer/*/viewWishlist', array('_current' => true)),
        ));
    }

    /**
     * Get customer data from session or service.
     *
     * @param int|null $customerId possible customer ID from DB
     * @return Customer
     * @throws NoSuchEntityException
     */
    protected function getCustomer($customerId)
    {
        $customerData = $this->_backendSession->getCustomerData();
        if (!empty($customerData['account'])) {
            return $this->_customerBuilder->populateWithArray($customerData['account'])->create();
        } elseif ($customerId) {
            return $this->_customerService->getCustomer($customerId);
        } else {
            return new Customer([]);
        }
    }
}
