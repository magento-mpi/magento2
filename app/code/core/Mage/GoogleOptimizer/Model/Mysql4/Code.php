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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Googleoptimizer_Model_Mysql4_Code extends Mage_Core_Model_Mysql4_Abstract
{
    protected function  _construct()
    {
        $this->_init('googleoptimizer/code', 'code_id');
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $googleOptimizer
     * @param unknown_type $productId
     * @return unknown
     */
    public function loadbyEntityType($googleOptimizer, $entity)
    {
        $read = $this->_getReadAdapter();
        if ($read) {
            $select = $read->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable().'.entity_id=?', $entity->getId())
                ->where($this->getMainTable().'.entity_type=?', $googleOptimizer->getEntityType())
//                ->where($this->getMainTable().'.store_id=?', $entity->getStoreId())
                ->where($this->getMainTable().'.store_id=?', 0)
                ->limit(1);
            $data = $read->fetchRow($select);
//            if (!$data && $entity->getStoreId() != '0') {
//                $select->reset('where');
//                $select->where($this->getMainTable().'.entity_id=?', $entity->getId())
//                    ->where($this->getMainTable().'.entity_type=?', $googleOptimizer->getEntityType())
//                    ->where($this->getMainTable().'.store_id=?', 0);
//
//                $data = $read->fetchRow($select);
//            }
            if ($data) {
                $googleOptimizer->setData($data);
            }
        }
        $this->_afterLoad($googleOptimizer);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $googleoptimizer
     * @param unknown_type $entity
     * @return unknown
     */
    public function deleteByEntityType($googleoptimizer, $entity)
    {
        $write = $this->_getWriteAdapter();
        if ($write) {
            $where = $write->quoteInto($this->getMainTable().'.entity_id=?', $entity->getId()) .
                ' AND ' . $write->quoteInto($this->getMainTable().'.entity_type=?', $entity->getEntityType());
            $write->delete($this->getMainTable(), $where);
        }

        $this->_afterDelete($googleoptimizer);
        return $this;
    }
}