<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag Relation resource model
 *
 * @category   Mage
 * @package    Mage_Tag
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