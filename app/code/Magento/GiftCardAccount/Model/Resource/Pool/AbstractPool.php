<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftCardAccount Pool Resource Model Abstract
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCardAccount\Model\Resource\Pool;

abstract class AbstractPool extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Delete records in db using specified status as criteria
     *
     * @param int $status
     * @return \Magento\GiftCardAccount\Model\Resource\Pool\AbstractPool
     */
    public function cleanupByStatus($status)
    {
        $where = array('status = ?' => $status);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }
}
