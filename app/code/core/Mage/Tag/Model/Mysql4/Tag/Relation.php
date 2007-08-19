<?php
/**
 * Tag Relation resource model
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Model_Mysql4_Tag_Relation extends Mage_Core_Model_Mysql4_Abstract
{
    public function __construct()
    {
        $this->_init('tag/relation', 'tag_relation_id');
    }

    public function loadByTagCustomer($model)
    {
        if( $model->getTagId() && $model->getCustomerId() ) {
            $read = $this->getConnection('read');
            $select = $read->select();

            $select->from($this->getMainTable())
                ->join($this->getTable('tag/tag'), "{$this->getTable('tag/tag')}.tag_id = {$this->getMainTable()}.tag_id")
                ->where("{$this->getMainTable()}.tag_id = ?", $model->getTagId())
                ->where('customer_id = ?', $model->getCustomerId());

            if( $model->getProductId() ) {
                $select->where("product_id = ?", $model->getProductId());
            }

            $data = $read->fetchRow($select);
            $model->setData( ( is_array($data) ) ? $data : array() );
            return $this;
        } else {
            return $this;
        }
    }
}