<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\View;

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;
use Magento\Eav\Model\AttributeDataFactory;

/**
 * Order history block
 */
class Info extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * Customer service
     *
     * @var CustomerMetadataServiceInterface
     */
    protected $_customerMetadataService;

    /**
     * Group service
     *
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * Metadata element factory
     *
     * @var \Magento\Customer\Model\Metadata\ElementFactory
     */
    protected $_metadataElementFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param CustomerMetadataServiceInterface $customerMetadataService
     * @param \Magento\Customer\Model\Metadata\ElementFactory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        CustomerMetadataServiceInterface $customerMetadataService,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        array $data = array()
    ) {
        $this->_groupService = $groupService;
        $this->_customerMetadataService = $customerMetadataService;
        $this->_metadataElementFactory = $elementFactory;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Retrieve required options from parent
     *
     * @return void
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Model\Exception(__('Please correct the parent block for this block.'));
        }
        $this->setOrder($this->getParentBlock()->getOrder());

        foreach ($this->getParentBlock()->getOrderInfoData() as $k => $v) {
            $this->setDataUsingMethod($k, $v);
        }

        parent::_beforeToHtml();
    }

    /**
     * Get order store name
     *
     * @return null|string
     */
    public function getOrderStoreName()
    {
        if ($this->getOrder()) {
            $storeId = $this->getOrder()->getStoreId();
            if (is_null($storeId)) {
                $deleted = __(' [deleted]');
                return nl2br($this->getOrder()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = array($store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName());
            return implode('<br/>', $name);
        }
        return null;
    }

    /**
     * Return name of the customer group.
     *
     * @return string
     */
    public function getCustomerGroupName()
    {
        if ($this->getOrder()) {
            $customerGroupId = $this->getOrder()->getCustomerGroupId();
            if (!is_null($customerGroupId)) {
                return $this->_groupService->getGroup($customerGroupId)->getCode();
            }
        }
        return '';
    }

    /**
     * Get URL to edit the customer.
     *
     * @return string
     */
    public function getCustomerViewUrl()
    {
        if ($this->getOrder()->getCustomerIsGuest() || !$this->getOrder()->getCustomerId()) {
            return '';
        }
        return $this->getUrl('customer/index/edit', array('id' => $this->getOrder()->getCustomerId()));
    }

    /**
     * Get order view URL.
     *
     * @param int $orderId
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', array('order_id' => $orderId));
    }

    /**
     * Find sort order for account data
     * Sort Order used as array key
     *
     * @param array $data
     * @param int $sortOrder
     * @return int
     */
    protected function _prepareAccountDataSortOrder(array $data, $sortOrder)
    {
        if (isset($data[$sortOrder])) {
            return $this->_prepareAccountDataSortOrder($data, $sortOrder + 1);
        }
        return $sortOrder;
    }

    /**
     * Return array of additional account data
     * Value is option style array
     *
     * @return array
     */
    public function getCustomerAccountData()
    {
        $accountData = array();
        $entityType = 'customer';

        foreach ($this->_customerMetadataService->getAllCustomerAttributeMetadata($entityType) as $attribute) {
            /* @var $attribute \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata */
            if (!$attribute->isVisible() || $attribute->isSystem()) {
                continue;
            }
            $orderKey = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $this->getOrder()->getData($orderKey);
            if ($orderValue != '') {
                $metadataElement = $this->_metadataElementFactory->create($attribute, $orderValue, $entityType);
                $value = $metadataElement->outputValue(AttributeDataFactory::OUTPUT_FORMAT_HTML);
                $sortOrder = $attribute->getSortOrder() + $attribute->isUserDefined() ? 200 : 0;
                $sortOrder = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = array(
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, array('br'))
                );
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * Get link to edit order address page
     *
     * @param \Magento\Sales\Model\Order\Address $address
     * @param string $label
     * @return string
     */
    public function getAddressEditLink($address, $label = '')
    {
        if ($this->_authorization->isAllowed('Magento_Sales::actions_edit')) {
            if (empty($label)) {
                $label = __('Edit');
            }
            $url = $this->getUrl('sales/order/address', array('address_id' => $address->getId()));
            return '<a href="' . $url . '">' . $label . '</a>';
        }
        return '';
    }

    /**
     * Whether Customer IP address should be displayed on sales documents
     *
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !$this->_scopeConfig->isSetFlag(
            'sales/general/hide_customer_ip',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getOrder()->getStoreId()
        );
    }

    /**
     * Check if is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
