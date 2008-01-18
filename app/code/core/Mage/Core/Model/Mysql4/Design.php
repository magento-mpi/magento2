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
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Mysql4_Design extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('core/design_change', 'design_change_id');
	}

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setDateFrom($this->formatDate($object->getDateFrom()));
        $object->setDateTo($this->formatDate($object->getDateTo()));

        if (strtotime($object->getDateFrom()) > strtotime($object->getDateTo())){
            Mage::throwException(Mage::helper('core')->__('Start date can\'t be greater than end date'));
        }

        $check = $this->_checkIntersection(
            $object->getStoreId(),
            $object->getDateFrom(),
            $object->getDateTo(),
            $object->getId()
        );

        if ($check){
            Mage::throwException(Mage::helper('core')
                ->__('Your design change for the specified store intersects with another one, please specify another date range')
            );
        }

        parent::_beforeSave($object);
    }

    private function _checkIntersection($storeId, $dateFrom, $dateTo, $currentId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table'=>$this->getTable('design_change')))

            ->where('main_table.store_id = ?', $storeId)
            ->where('main_table.design_change_id <> ?', $currentId)
            ->where('((date_from <= ? AND date_to >= ?)
                    OR
                    (date_from <= ? AND date_to >= ?)
                    OR
                    (date_from <= ? AND date_to >= ?)
                    OR
                    (date_from >= ? AND date_to <= ?))',

            $dateFrom, $dateFrom,  $dateTo, $dateTo, $dateFrom, $dateTo, $dateFrom, $dateTo);

        return $this->_getReadAdapter()->fetchOne($select);
    }
}
