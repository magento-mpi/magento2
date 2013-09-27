<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for "Reward Points Balance"
 *
 */
class Magento_Reward_Model_System_Config_Backend_Balance extends Magento_Core_Model_Config_Value
{
    /**
     * Check if max_points_balance >= than min_points_balance
     * (max allowed to RP to gain is more than minimum to redeem)
     *
     * @return Magento_Reward_Model_System_Config_Backend_Balance
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        if ($this->getFieldsetDataValue('min_points_balance') < 0) {
            $message = __('"Minimum Reward Points Balance" should be either a positive number or left empty.');
            throw new Magento_Core_Exception($message);
        }
        if ($this->getFieldsetDataValue('max_points_balance') < 0) {
            $message = __('"Cap Reward Points Balance" should be either a positive number or left empty.');
            throw new Magento_Core_Exception($message);
        }
        if ($this->getFieldsetDataValue('max_points_balance') &&
            ($this->getFieldsetDataValue('min_points_balance') > $this->getFieldsetDataValue('max_points_balance'))) {
            $message = __('"Minimum Reward Points Balance" should be less or equal to "Cap Reward Points Balance".');
            throw new Magento_Core_Exception($message);
        }
        return $this;
    }
}
