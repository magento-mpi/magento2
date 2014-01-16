<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order history block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\View;

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;

class Info extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * Customer service
     *
     * @var CustomerMetadataServiceInterface
     */
    protected $_customerMetadataService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var \Magento\Eav\Model\AttributeDataFactory
     */
    protected $_attrDataFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\AttributeDataFactory $attrDataFactory
     * @param CustomerMetadataServiceInterface $customerMetadataService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\AttributeDataFactory $attrDataFactory,
        CustomerMetadataServiceInterface $customerMetadataService,
        array $data = array()
    ) {
        $this->_groupService = $groupService;
        $this->_eavConfig = $eavConfig;
        $this->_attrDataFactory = $attrDataFactory;
        $this->_customerMetadataService = $customerMetadataService;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Core\Exception(__('Please correct the parent block for this block.'));
        }
        $this->setOrder($this->getParentBlock()->getOrder());

        foreach ($this->getParentBlock()->getOrderInfoData() as $k => $v) {
            $this->setDataUsingMethod($k, $v);
        }

        parent::_beforeToHtml();
    }

    public function getOrderStoreName()
    {
        if ($this->getOrder()) {
            $storeId = $this->getOrder()->getStoreId();
            if (is_null($storeId)) {
                $deleted = __(' [deleted]');
                return nl2br($this->getOrder()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = array(
                $store->getWebsite()->getName(),
                $store->getGroup()->getName(),
                $store->getName()
            );
            return implode('<br/>', $name);
        }
        return null;
    }

    public function getCustomerGroupName()
    {
        if ($this->getOrder()) {
            $customerGroupId = $this->getOrder()->getCustomerGroupId();
            if (!is_null($customerGroupId)) {
                return $this->_groupService->getGroup($customerGroupId)->getCode();
            }
        }
        return null;
    }

    public function getCustomerViewUrl()
    {
        if ($this->getOrder()->getCustomerIsGuest() || !$this->getOrder()->getCustomerId()) {
            return false;
        }
        return $this->getUrl('customer/index/edit', array('id' => $this->getOrder()->getCustomerId()));
    }

    public function getViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', array('order_id'=>$orderId));
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
            /* @var $attribute \Magento\Customer\Model\Attribute */
            if (!$attribute->getIsVisible() || $attribute->getIsSystem()) {
                continue;
            }
            $orderKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $this->getOrder()->getData($orderKey);
            if ($orderValue != '') {
                $dataModel = $this->_elementFactory->create(
                    $attribute,
                    isset($this->_attributeValues[$attribute->getAttributeCode()]),
                    $this->_entityType
                );
                $value      = $dataModel->outputValue(\Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML);
                $sortOrder  = $attribute->getSortOrder() + $attribute->getIsUserDefined() ? 200 : 0;
                $sortOrder  = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
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
    public function getAddressEditLink($address, $label='')
    {
        if (empty($label)) {
            $label = __('Edit');
        }
        $url = $this->getUrl('sales/order/address', array('address_id'=>$address->getId()));
        return '<a href="'.$url.'">' . $label . '</a>';
    }

    /**
     * Whether Customer IP address should be displayed on sales documents
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !$this->_storeConfig
            ->getConfigFlag('sales/general/hide_customer_ip', $this->getOrder()->getStoreId());
    }

    /**
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
