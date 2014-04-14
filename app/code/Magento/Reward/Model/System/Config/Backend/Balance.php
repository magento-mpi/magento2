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

use Magento\Model\Exception;

class Balance extends \Magento\App\Config\Value
{
    /**
     * Check if max_points_balance >= than min_points_balance
     * (max allowed to RP to gain is more than minimum to redeem)
     *
     * @return $this
     * @throws Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        if ($this->getFieldsetDataValue('min_points_balance') < 0) {
            $message = __('"Minimum Reward Points Balance" should be either a positive number or left empty.');
            throw new Exception($message);
        }
        if ($this->getFieldsetDataValue('max_points_balance') < 0) {
            $message = __('"Cap Reward Points Balance" should be either a positive number or left empty.');
            throw new Exception($message);
        }
        if ($this->getFieldsetDataValue(
            'max_points_balance'
        ) && $this->getFieldsetDataValue(
            'min_points_balance'
        ) > $this->getFieldsetDataValue(
            'max_points_balance'
        )
        ) {
            $message = __('"Minimum Reward Points Balance" should be less or equal to "Cap Reward Points Balance".');
            throw new Exception($message);
        }
        return $this;
    }
}
