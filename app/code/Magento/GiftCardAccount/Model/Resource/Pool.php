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
 * GiftCard pool resource model
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCardAccount\Model\Resource;

class Pool extends \Magento\GiftCardAccount\Model\Resource\Pool\AbstractPool
{
    /**
     * Define main table and primary key field
     *
     */
    protected function _construct()
    {
        $this->_init('magento_giftcardaccount_pool', 'code');
    }

    /**
     * Save some code
     *
     * @param string $code
     */
    public function saveCode($code)
    {
        $field = $this->getIdFieldName();
        $this->_getWriteAdapter()->insert(
            $this->getMainTable(),
            array($field => $code)
        );
    }

    /**
     * Check if code exists
     *
     * @param string $code
     * @return bool
     */
    public function exists($code)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getMainTable(), $this->getIdFieldName());
        $select->where($this->getIdFieldName() . ' = :code');

        if ($read->fetchOne($select, array('code' => $code)) === false) {
            return false;
        }
        return true;
    }
}
