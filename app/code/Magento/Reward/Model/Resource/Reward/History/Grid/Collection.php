<?php
    /**
     * Reward rate collection for customer edit tab history grid
     *
     * {license_notice}
     *
     * @copyright   {copyright}
     * @license     {license_link}
     */
class Magento_Reward_Model_Resource_Reward_History_Grid_Collection
    extends Magento_Reward_Model_Resource_Reward_History_Collection
{
    /**
     * @var Magento_Reward_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Reward_Helper_Data $helper
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Reward_Helper_Data $helper,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_helper = $helper;
        parent::__construct($eventManager, $fetchStrategy, $resource);
    }

    /**
     * @return Magento_Reward_Model_Resource_Reward_History_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        /** @var $collection Magento_Reward_Model_Resource_Reward_History_Collection */
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
     * @return Magento_Reward_Model_Resource_Reward_History_Grid_Collection
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
