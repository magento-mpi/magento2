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
 * GiftCard account resource model
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCardAccount_Model_Resource_Giftcardaccount extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table  and primary key field
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_giftcardaccount', 'giftcardaccount_id');
    }

    /**
     * Get gift card account ID by specified code
     *
     * @param string $code
     * @return mixed
     */
    public function getIdByCode($code)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getMainTable(), $this->getIdFieldName());
        $select->where('code = :code');

        if ($id = $read->fetchOne($select, array('code' => $code))) {
            return $id;
        }

        return false;
    }

    /**
     * Update gift card accounts state
     *
     * @param array $ids
     * @param int $state
     * @return Enterprise_GiftCardAccount_Model_Resource_Giftcardaccount
     */
    public function updateState($ids, $state)
    {
        if (empty($ids)) {
            return $this;
        }
        $bind = array('state' => $state);
        $where[$this->getIdFieldName() . ' IN (?)'] = $ids;

        $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);
        return $this;
    }
}
