<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model\Resource\Pool;

/**
 * GiftCardAccount Pool Resource Model Abstract
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractPool extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Delete records in db using specified status as criteria
     *
     * @param int $status
     * @return $this
     */
    public function cleanupByStatus($status)
    {
        $where = array('status = ?' => $status);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }
}
