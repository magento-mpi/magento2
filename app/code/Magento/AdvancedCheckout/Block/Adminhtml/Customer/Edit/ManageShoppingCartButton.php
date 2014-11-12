<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Customer\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\StoreManagerInterface;
use Magento\Ui\Component\Control\ButtonProviderInterface;

/**
 * Additional buttons on customer edit form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ManageShoppingCartButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Backend\Block\Widget\Button\ButtonList
     */
    protected $buttonList;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        StoreManagerInterface $storeManager
    ) {
        $this->buttonList = $context->getButtonList();
        $this->authorization = $context->getAuthorization();
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $customerWebsite = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER)->getWebsiteId();
        $data = [];
        if ($this->getCustomerId() &&
            $this->authorization->isAllowed(
                'Magento_AdvancedCheckout::view'
            ) && $this->authorization->isAllowed(
                'Magento_AdvancedCheckout::update'
            ) && $this->storeManager->getStore(
                \Magento\Store\Model\Store::ADMIN_CODE
            )->getWebsiteId() != $customerWebsite
        ) {
            $data =  [
                'label' => __('Manage Shopping Cart'),
                'on_click' => 'setLocation(\'' . $this->getManageShoppingCartUrl() . '\')',
                'sort_order' => 70
            ];
        }
        return $data;
    }

    public function getManageShoppingCartUrl()
    {
        return $this->urlBuilder->getUrl('checkout/index', array('customer' => $this->getCustomerId()));
    }

    /**
     * Return the customer Id.
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        $customerId = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        return $customerId;
    }
}
