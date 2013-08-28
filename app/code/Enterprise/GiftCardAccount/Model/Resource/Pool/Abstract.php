<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftCardAccount Pool Resource Model Abstract
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_GiftCardAccount_Model_Resource_Pool_Abstract extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Delete records in db using specified status as criteria
     *
     * @param int $status
     * @return Enterprise_GiftCardAccount_Model_Resource_Pool_Abstract
     */
    public function cleanupByStatus($status)
    {
        $where = array('status = ?' => $status);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }
}
