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
     * @param Magento_Core_Model_Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param \Magento\Core\Model\Registry $registryManager
     * @param \Magento\Core\Model\Fieldset\Config $fieldsetConfig
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        \Magento\Core\Model\Registry $registryManager,
        \Magento\Core\Model\Fieldset\Config $fieldsetConfig
    ) {
        $this->_registryManager = $registryManager;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $fieldsetConfig);
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
