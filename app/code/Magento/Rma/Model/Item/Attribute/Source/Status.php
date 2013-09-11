<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item status attribute model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Item\Attribute\Source;

class Status extends \Magento\Rma\Model\Rma\Source\Status
{
    /**
     * Get available states keys for entities
     *
     * @return array
     */
    protected function _getAvailableValues()
    {
        return array(
            self::STATE_PENDING,
            self::STATE_AUTHORIZED,
            self::STATE_RECEIVED,
            self::STATE_APPROVED,
            self::STATE_REJECTED,
            self::STATE_DENIED,
        );
    }

    /**
     * Checks is status available
     *
     * @param string $status RMA item status
     * @return boolean
     */
    public function checkStatus($status)
    {
        return in_array($status, $this->_getAvailableValues()) ? true : false;
    }

    /**
     * Checks is status final
     *
     * @param string $status RMA item status
     * @return boolean
     */
    public function isFinalStatus($status)
    {
        return in_array($status, array(self::STATE_APPROVED, self::STATE_REJECTED, self::STATE_DENIED)) ? true : false;
    }
}
