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
 * GiftCard pool resource model
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCardAccount_Model_Resource_Pool extends Enterprise_GiftCardAccount_Model_Resource_Pool_Abstract
{
    /**
     * Define main table and primary key field
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_giftcardaccount_pool', 'code');
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
