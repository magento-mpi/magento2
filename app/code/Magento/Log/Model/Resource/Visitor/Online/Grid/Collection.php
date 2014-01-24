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
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Log\Model\Visitor\OnlineFactory $onlineFactory
     * @param mixed $connection
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Log\Model\Visitor\OnlineFactory $onlineFactory,
        $connection = null,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_onlineFactory = $onlineFactory;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $customerFactory,
            $connection,
            $resource
        );
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_onlineFactory->create()->prepare();
        $this->addCustomerData();
        return $this;
    }

}
