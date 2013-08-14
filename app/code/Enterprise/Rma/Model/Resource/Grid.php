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
 * RMA entity resource model
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Model_Resource_Grid extends Magento_Core_Model_Resource_Db_Abstract
{
    protected $_isPkAutoIncrement    = false;
    /**
     * Internal constructor
     */
    protected function _construct() {
        $this->_init('enterprise_rma_grid', 'entity_id');
    }
}
