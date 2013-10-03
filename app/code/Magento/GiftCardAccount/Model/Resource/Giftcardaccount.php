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
 * GiftCard account resource model
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCardAccount\Model\Resource;

class Giftcardaccount extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Define main table  and primary key field
     *
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
     * @return \Magento\GiftCardAccount\Model\Resource\Giftcardaccount
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
