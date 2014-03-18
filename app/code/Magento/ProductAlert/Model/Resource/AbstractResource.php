<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Model\Resource;

/**
 * Product alert for back in abstract resource model
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractResource extends \Magento\Model\Resource\Db\AbstractDb
{
    /**
     * Retrieve alert row by object parameters
     *
     * @param \Magento\Model\AbstractModel $object
     * @return array|false
     */
    protected function _getAlertRow(\Magento\Model\AbstractModel $object)
    {
        $adapter = $this->_getReadAdapter();
        if ($object->getCustomerId() && $object->getProductId() && $object->getWebsiteId()) {
            $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('customer_id = :customer_id')
                ->where('product_id  = :product_id')
                ->where('website_id  = :website_id');
            $bind = array(
                ':customer_id' => $object->getCustomerId(),
                ':product_id'  => $object->getProductId(),
                ':website_id'  => $object->getWebsiteId()
            );
            return $adapter->fetchRow($select, $bind);
        }
        return false;
    }

    /**
     * Load object data by parameters
     *
     * @param \Magento\Model\AbstractModel $object
     * @return $this
     */
    public function loadByParam(\Magento\Model\AbstractModel $object)
    {
        $row = $this->_getAlertRow($object);
        if ($row) {
            $object->setData($row);
        }
        return $this;
    }

    /**
     * Delete all customer alerts on website
     *
     * @param \Magento\Model\AbstractModel $object
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     */
    public function deleteCustomer(\Magento\Model\AbstractModel $object, $customerId, $websiteId=null)
    {
        $adapter = $this->_getWriteAdapter();
        $where   = array();
        $where[] = $adapter->quoteInto('customer_id=?', $customerId);
        if ($websiteId) {
            $where[] = $adapter->quoteInto('website_id=?', $websiteId);
        }
        $adapter->delete($this->getMainTable(), $where);
        return $this;
    }
}
