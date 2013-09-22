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

class Collection
    extends \Magento\Reward\Model\Resource\Reward\History\Collection
{
    /**
     * @var \Magento\Reward\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Reward_Helper_Data $helper
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        \Magento\Reward\Helper\Data $helper,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_helper = $helper;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * @return \Magento\Reward\Model\Resource\Reward\History\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        /** @var $collection \Magento\Reward\Model\Resource\Reward\History\Collection */
        $this->setExpiryConfig($this->_helper->getExpiryConfig())
            ->addExpirationDate()
            ->setOrder('history_id', 'desc');
        $this->setDefaultOrder();
        return $this;
    }

    /**
     * Add column filter to collection
     *
     * @param array|string $field
     * @param null $condition
     * @return \Magento\Reward\Model\Resource\Reward\History\Grid\Collection
     */
    public  function addFieldToFilter($field, $condition = null)
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
