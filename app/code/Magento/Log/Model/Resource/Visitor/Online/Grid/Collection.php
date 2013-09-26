<?php
/**
 * Log Online visitors collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\Resource\Visitor\Online\Grid;

class Collection extends \Magento\Log\Model\Resource\Visitor\Online\Collection
{
    /**
     * @var \Magento\Log\Model\Visitor\OnlineFactory
     */
    protected $_onlineFactory;

    /**
     * @param \Magento\Log\Model\Visitor\OnlineFactory $onlineFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Log\Model\Visitor\OnlineFactory $onlineFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_onlineFactory = $onlineFactory;
        parent::__construct($customerFactory, $eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * @return \Magento\Log\Model\Resource\Visitor\Online\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_onlineFactory->create()->prepare();
        $this->addCustomerData();
        return $this;
    }

}
