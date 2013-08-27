<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA shipping collection
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Model_Resource_Shipping_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Rma_Model_Shipping', 'Enterprise_Rma_Model_Resource_Shipping');
    }
}
