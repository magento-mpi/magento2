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
namespace Magento\Reward\Model\System\Config\Backend;

class Balance extends \Magento\Core\Model\Config\Value
{
    /**
     * Check if max_points_balance >= than min_points_balance
     * (max allowed to RP to gain is more than minimum to redeem)
     *
     * @return \Magento\Reward\Model\System\Config\Backend\Balance
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        if ($this->getFieldsetDataValue('min_points_balance') < 0) {
            \Mage::throwException(__('"Minimum Reward Points Balance" should be either a positive number or left empty.'));
        }
        if ($this->getFieldsetDataValue('max_points_balance') < 0) {
            \Mage::throwException(__('"Cap Reward Points Balance" should be either a positive number or left empty.'));
        }
        if ($this->getFieldsetDataValue('max_points_balance') &&
            ($this->getFieldsetDataValue('min_points_balance') > $this->getFieldsetDataValue('max_points_balance'))) {
            \Mage::throwException(__('"Minimum Reward Points Balance" should be less or equal to "Cap Reward Points Balance".'));
        }
        return $this;
    }
}
