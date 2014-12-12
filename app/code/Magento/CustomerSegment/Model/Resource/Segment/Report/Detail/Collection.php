<?php
/**
 * Customer segment report detail collection
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Resource\Segment\Report\Detail;

class Collection extends \Magento\CustomerSegment\Model\Resource\Report\Customer\Collection
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registryManager;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Framework\Object\Copy\Config $fieldsetConfig
     * @param \Magento\Framework\Registry $registryManager
     * @param mixed $connection
     * @param string $modelName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\Object\Copy\Config $fieldsetConfig,
        \Magento\Framework\Registry $registryManager,
        $connection = null,
        $modelName = self::CUSTOMER_MODEL_NAME
    ) {
        $this->_registryManager = $registryManager;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $fieldsetConfig,
            $connection,
            $modelName
        );
    }

    /**
     * @return \Magento\CustomerSegment\Model\Resource\Segment\Report\Detail\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addNameToSelect()->setViewMode(
            $this->getCustomerSegment()->getViewMode()
        )->addSegmentFilter(
            $this->getCustomerSegment()
        )->addWebsiteFilter(
            $this->_registryManager->registry('filter_website_ids')
        )->joinAttribute(
            'billing_postcode',
            'customer_address/postcode',
            'default_billing',
            null,
            'left'
        )->joinAttribute(
            'billing_city',
            'customer_address/city',
            'default_billing',
            null,
            'left'
        )->joinAttribute(
            'billing_telephone',
            'customer_address/telephone',
            'default_billing',
            null,
            'left'
        )->joinAttribute(
            'billing_region',
            'customer_address/region',
            'default_billing',
            null,
            'left'
        )->joinAttribute(
            'billing_country_id',
            'customer_address/country_id',
            'default_billing',
            null,
            'left'
        );
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomerSegment()
    {
        return $this->_registryManager->registry('current_customer_segment');
    }
}
