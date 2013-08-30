<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tag Helper
 *
 * @category    Enterprise
 * @package     Enterprise_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Tag_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Reward points model instance
     *
     * @var Enterprise_Reward_Model_Reward
     */
    protected $_rewardModel;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        array $data = array()
    ) {
        if (isset($data['reward_model'])) {
            $this->_rewardModel = $data['reward_model'];
        } else {
            $this->_rewardModel = Mage::getSingleton('Enterprise_Reward_Model_Reward');
        }
        parent::__construct($context);
    }

    /**
     * Add tag action model to reward model actions list
     *
     * @return Enterprise_Tag_Helper_Data
     */
    public function addActionClassToRewardModel()
    {
        $this->_rewardModel->setActionModelClass(Enterprise_Tag_Model_Reward::REWARD_ACTION_TAG,
            Enterprise_Tag_Model_Reward::REWARD_ACTION_TAG_MODEL
        );

        return $this;
    }
}
