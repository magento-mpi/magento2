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
 * RMA shipping collection
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Resource_Shipping_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('Magento_Rma_Model_Shipping', 'Magento_Rma_Model_Resource_Shipping');
    }
}
