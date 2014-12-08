<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model\Resource;

/**
 * GiftCard pool resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Pool extends \Magento\GiftCardAccount\Model\Resource\Pool\AbstractPool
{
    /**
     * Define main table and primary key field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_giftcardaccount_pool', 'code');
    }

    /**
     * Save some code
     *
     * @param string $code
     * @return void
     */
    public function saveCode($code)
    {
        $field = $this->getIdFieldName();
        $this->_getWriteAdapter()->insert($this->getMainTable(), [$field => $code]);
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

        if ($read->fetchOne($select, ['code' => $code]) === false) {
            return false;
        }
        return true;
    }
}
