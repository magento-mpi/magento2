<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward rate collection
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Resource_Reward_Rate_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Reward rate model
     *
     * @var Enterprise_Reward_Model_Reward_Rate
     */
    protected $_rewardRateModel;

    /**
     * @param Enterprise_Reward_Model_Reward_Rate $rewardRate
     */
    public function __construct(Enterprise_Reward_Model_Reward_Rate $rewardRate)
    {
        $this->_rewardRateModel = $rewardRate;
    }

    /**
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_rewardRateModel->getCollection();
    }
}
