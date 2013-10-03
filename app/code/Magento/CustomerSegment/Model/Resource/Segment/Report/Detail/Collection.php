<?php
/**
 * Customer segment report detail collection
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Resource\Segment\Report\Detail;

class Collection
    extends \Magento\CustomerSegment\Model\Resource\Report\Customer\Collection
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Core\Model\Fieldset\Config $fieldsetConfig
     * @param \Magento\Core\Model\Registry $registryManager
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Core\Model\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Core\Model\Fieldset\Config $fieldsetConfig,
        \Magento\Core\Model\Registry $registryManager
    ) {
        $this->_registryManager = $registryManager;
        parent::__construct(
            $eventManager,
            $logger,
            $fetchStrategy,
            $entityFactory,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $fieldsetConfig
        );
    }

    /**
     * @return \Magento\CustomerSegment\Model\Resource\Segment\Report\Detail\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addNameToSelect()
            ->setViewMode($this->getCustomerSegment()->getViewMode())
            ->addSegmentFilter($this->getCustomerSegment())
            ->addWebsiteFilter($this->_registryManager->registry('filter_website_ids'))
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
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
