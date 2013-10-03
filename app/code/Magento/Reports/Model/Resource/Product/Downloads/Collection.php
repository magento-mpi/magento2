<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product Downloads Report collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Product\Downloads;

class Collection extends \Magento\Catalog\Model\Resource\Product\Collection
{
    /**
     * Identifier field name
     *
     * @var string
     */
    protected $_idFieldName    = 'link_id';

    /**
     * Add downloads summary grouping by product
     *
     * @return \Magento\Reports\Model\Resource\Product\Downloads\Collection
     */
    public function addSummary()
    {
        $adapter  = $this->getConnection();
        $linkExpr = $adapter->getIfNullSql('l_store.title', 'l.title');

        $this->getSelect()
            ->joinInner(
                array('d' =>  $this->getTable('downloadable_link_purchased_item')),
                'e.entity_id = d.product_id',
                array(
                    'purchases' => new \Zend_Db_Expr('SUM(d.number_of_downloads_bought)'),
                    'downloads' => new \Zend_Db_Expr('SUM(d.number_of_downloads_used)'),
                ))
            ->joinInner(
                array('l' => $this->getTable('downloadable_link_title')),
                'd.link_id = l.link_id',
                array('l.link_id'))
            ->joinLeft(
                array('l_store' => $this->getTable('downloadable_link_title')),
                $adapter->quoteInto('l.link_id = l_store.link_id AND l_store.store_id = ?', (int)$this->getStoreId()),
                array('link_title' => $linkExpr))
            ->where(implode(' OR ', array(
                $adapter->quoteInto('d.number_of_downloads_bought > ?', 0),
                $adapter->quoteInto('d.number_of_downloads_used > ?', 0),
            )))
            ->group('d.link_id');
        return $this;
    }

    /**
     * Add sorting
     *
     * @param string $attribute
     * @param string $dir
     * @return \Magento\Reports\Model\Resource\Product\Downloads\Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if ($attribute == 'purchases' || $attribute == 'downloads' || $attribute == 'link_title') {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    /**
     * Add filtering
     *
     * @param string $field
     * @param string $condition
     * @return \Magento\Reports\Model\Resource\Product\Downloads\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'link_title') {
            $conditionSql = $this->_getConditionSql('l.title', $condition);
            $this->getSelect()->where($conditionSql);
        } else {
            parent::addFieldToFilter($field, $condition);
        }
        return $this;
    }
}
