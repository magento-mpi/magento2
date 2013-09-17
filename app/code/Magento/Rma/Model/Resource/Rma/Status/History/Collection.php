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
 * RMA entity resource model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Resource_Rma_Status_History_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Rma_Model_Rma_Status_History', 'Magento_Rma_Model_Resource_Rma_Status_History');
    }
}
