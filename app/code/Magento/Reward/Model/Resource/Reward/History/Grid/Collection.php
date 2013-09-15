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
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Reward\Helper\Data $helper
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Reward\Helper\Data $helper,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_helper = $helper;
        parent::__construct($eventManager, $fetchStrategy, $resource);
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
