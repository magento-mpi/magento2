<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model;

class Index extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Target rule data
     *
     * @var \Magento\TargetRule\Helper\Data
     */
    protected $_targetRuleData = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\TargetRule\Model\Resource\Rule\CollectionFactory
     */
    protected $_ruleCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\TargetRule\Helper\Data $targetRuleData
     * @param \Magento\TargetRule\Model\Resource\Index $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $session,
        \Magento\TargetRule\Helper\Data $targetRuleData,
        \Magento\TargetRule\Model\Resource\Index $resource,
        \Magento\Framework\Data\Collection\Db $resourceCollection,
        array $data = array()
    ) {
        $this->_ruleCollectionFactory = $ruleFactory;
        $this->_storeManager = $storeManager;
        $this->_session = $session;
        $this->_targetRuleData = $targetRuleData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\TargetRule\Model\Resource\Index');
    }

    /**
     * Retrieve resource instance
     *
     * @return \Magento\TargetRule\Model\Resource\Index
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Set Catalog Product List identifier
     *
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData('type', $type);
    }

    /**
     * Retrieve Catalog Product List identifier
     *
     * @throws \Magento\Framework\Model\Exception
     * @return int
     */
    public function getType()
    {
        $type = $this->getData('type');
        if (is_null($type)) {
            throw new \Magento\Framework\Model\Exception(__('Undefined Catalog Product List Type'));
        }
        return $type;
    }

    /**
     * Set store scope
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * Retrieve store identifier scope
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->getData('store_id');
        if (is_null($storeId)) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Set customer group identifier
     *
     * @param int $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData('customer_group_id', $customerGroupId);
    }

    /**
     * Retrieve customer group identifier
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        $customerGroupId = $this->getData('customer_group_id');
        if (is_null($customerGroupId)) {
            $customerGroupId = $this->_session->getCustomerGroupId();
        }
        return $customerGroupId;
    }

    /**
     * Set result limit
     *
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        return $this->setData('limit', $limit);
    }

    /**
     * Retrieve result limit
     *
     * @return int
     */
    public function getLimit()
    {
        $limit = $this->getData('limit');
        if (is_null($limit)) {
            $limit = $this->_targetRuleData->getMaximumNumberOfProduct($this->getType());
        }
        return $limit;
    }

    /**
     * Set Product data object
     *
     * @param \Magento\Framework\Object $product
     * @return $this
     */
    public function setProduct(\Magento\Framework\Object $product)
    {
        return $this->setData('product', $product);
    }

    /**
     * Retrieve Product data object
     *
     * @return \Magento\Framework\Object
     * @throws \Magento\Framework\Model\Exception
     */
    public function getProduct()
    {
        $product = $this->getData('product');
        if (!$product instanceof \Magento\Framework\Object) {
            throw new \Magento\Framework\Model\Exception(__('Please define a product data object.'));
        }
        return $product;
    }

    /**
     * Set product ids list be excluded
     *
     * @param int|array $productIds
     * @return $this
     */
    public function setExcludeProductIds($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        return $this->setData('exclude_product_ids', $productIds);
    }

    /**
     * Retrieve Product Ids which must be excluded
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = $this->getData('exclude_product_ids');
        if (!is_array($productIds)) {
            $productIds = array();
        }
        return $productIds;
    }

    /**
     * Retrieve related product Ids
     *
     * @return array
     */
    public function getProductIds()
    {
        return $this->_getResource()->getProductIds($this);
    }

    /**
     * Retrieve Rule collection by type and product
     *
     * @return \Magento\TargetRule\Model\Resource\Rule\Collection
     */
    public function getRuleCollection()
    {
        /* @var $collection \Magento\TargetRule\Model\Resource\Rule\Collection */
        $collection = $this->_ruleCollectionFactory->create();
        $collection->addApplyToFilter(
            $this->getType()
        )->addProductFilter(
            $this->getProduct()->getId()
        )->addIsActiveFilter()->setPriorityOrder()->setFlag(
            'do_not_run_after_load',
            true
        );

        return $collection;
    }

    /**
     * Retrieve SELECT instance for conditions
     *
     * @return \Magento\Framework\DB\Select
     */
    public function select()
    {
        return $this->_getResource()->select();
    }
}
