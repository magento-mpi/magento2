<?php
/**
 * Reward rate collection for customer edit tab history grid
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Resource\Reward\History\Grid;

class Collection extends \Magento\Reward\Model\Resource\Reward\History\Collection
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Reward\Helper\Data $helper
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Locale\ResolverInterface $localeResolver,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Reward\Helper\Data $helper,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_helper = $helper;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $localeResolver,
            $customerFactory,
            $dateTime,
            $connection,
            $resource
        );
    }

    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        /** @var $collection \Magento\Reward\Model\Resource\Reward\History\Collection */
        $this->setExpiryConfig($this->_helper->getExpiryConfig())->addExpirationDate()->setOrder('history_id', 'desc');
        $this->setDefaultOrder();
        return $this;
    }

    /**
     * Add column filter to collection
     *
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'website_id' || $field == 'points_balance') {
            if ($field && isset($condition)) {
                $this->addFieldToFilter('main_table.' . $field, $condition);
            }
        } else {
            parent::addFieldToFilter($field, $condition);
        }

        return $this;
    }
}
