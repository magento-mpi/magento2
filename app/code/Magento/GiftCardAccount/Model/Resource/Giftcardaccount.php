<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model\Resource;

/**
 * GiftCard account resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Giftcardaccount extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Define main table  and primary key field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_giftcardaccount', 'giftcardaccount_id');
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
     * @return $this
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
