<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Model\Resource\Pool;

/**
 * GiftCardAccount Pool Resource Model Abstract
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractPool extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Delete records in db using specified status as criteria
     *
     * @param int $status
     * @return $this
     */
    public function cleanupByStatus($status)
    {
        $where = ['status = ?' => $status];
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }
}
