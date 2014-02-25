<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource\Type;

/**
 * Gift refistry type resource collection
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * If the table was joined flag
     *
     * @var bool
     */
    protected $_isTableJoined                       = false;

    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftRegistry\Model\Type', 'Magento\GiftRegistry\Model\Resource\Type');
    }

    /**
     * Add store data to collection
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreData($storeId = \Magento\Core\Model\Store::DEFAULT_STORE_ID)
    {
        $infoTable = $this->getTable('magento_giftregistry_type_info');
        $adapter   = $this->getConnection();

        $select = $adapter->select();
        $select->from(array('m' => $this->getMainTable()))
            ->joinInner(
                array('d' => $infoTable),
                $adapter->quoteInto('m.type_id = d.type_id AND d.store_id = ?',
                    \Magento\Core\Model\Store::DEFAULT_STORE_ID),
                array())
            ->joinLeft(
                array('s' => $infoTable),
                $adapter->quoteInto('s.type_id = m.type_id AND s.store_id = ?', (int)$storeId),
                array(
                    'label'     => $adapter->getCheckSql('s.label IS NULL', 'd.label', 's.label'),
                    'is_listed' => $adapter->getCheckSql('s.is_listed IS NULL', 'd.is_listed', 's.is_listed'),
                    'sort_order'=> $adapter->getCheckSql('s.sort_order IS NULL', 'd.sort_order', 's.sort_order')
            ));

        $this->getSelect()->reset()->from(array('main_table' => $select));

        $this->_isTableJoined = true;

        return $this;
    }

    /**
     * Filter collection by listed param
     *
     * @return $this
     */
    public function applyListedFilter()
    {
        if ($this->_isTableJoined) {
            $this->getSelect()->where('is_listed = ?', 1);
        }
        return $this;
    }

    /**
     * Apply sorting by sort_order param
     *
     * @return $this
     */
    public function applySortOrder()
    {
        if ($this->_isTableJoined) {
            $this->getSelect()->order('sort_order');
        }
        return $this;
    }

    /**
     * Convert collection to array for select options
     *
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionArray($withEmpty = false)
    {
        $result = $this->_toOptionArray('type_id', 'label');
        if ($withEmpty) {
            $result = array_merge(array(array(
                'value' => '',
                'label' => __('-- All --')
            )), $result);
        }
        return $result;
    }
}
