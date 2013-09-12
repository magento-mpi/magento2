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
 * RMA model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Grid extends Magento_Core_Model_Abstract
{
    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Rma_Model_Resource_Grid');
        parent::_construct();
    }

    /**
     * Get available states keys for items
     *
     * @return array
     */
    protected function _getAvailableStates()
    {
        return array(
            self::STATE_PENDING,
            self::STATE_AUTHORIZED,
            self::STATE_RECEIVED,
            self::STATE_APPROVED,
            self::STATE_DENIED,
            self::STATE_REJECTED,
            self::STATE_CLOSED
        );
    }

    /**
     * Get RMA's status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if (is_null(parent::getStatusLabel())) {
            $this->setStatusLabel(Mage::getModel('Magento_Rma_Model_Rma_Source_Status')->getItemLabel($this->getStatus()));
        }
        return parent::getStatusLabel();
    }
}
