<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review statuses collection
 *
 * @category    Magento
 * @package     Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Review_Model_Resource_Review_Status_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Review status table
     *
     * @var string
     */
    protected $_reviewStatusTable;

    /**
     * Collection model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Review_Model_Review_Status', 'Magento_Review_Model_Resource_Review_Status');
    }

    /**
     * Convert items array to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('status_id', 'status_code');
    }
}
