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
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports Viewed Product Index Resource Model
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Product_Index_Viewed extends Mage_Reports_Model_Resource_Product_Index_Abstract
{
    /**
     * Initialize connection and main resource table
     *
     */
    protected function _construct()
    {
        $this->_init('reports/viewed_product_index', 'index_id');
    }

    /**
     *
     * Get unique columns
     * @return array
     */
    protected function _getUniqColumns()
    {
        return array(array('visitor_id', 'product_id'), array('customer_id', 'product_id'));
    }


    public function save(Mage_Core_Model_Abstract  $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }

        $this->_serializeFields($object);
        $this->_beforeSave($object);
        $this->_checkUnique($object);


        $data = $this->_prepareDataForSave($object);
        unset($data[$this->getIdFieldName()]);

        $checkSelect = $this->_getWriteAdapter()->select();
        $checkSelect->from(array('main_table' => $this->getMainTable()))
            ->where('main_table.product_id = ?' , $object->getProductId());
        $updateData = $data;
        unset($updateData['product_id']);

        if (!$object->getCustomerId()) {
            $checkSelect->where('main_table.visitor_id = ?', $object->getVisitorId());
        } else {
            $checkSelect->where('main_table.customer_id=? OR main_table.customer_id IS NULL', $object->getCustomerId());
        }

        $checkSelect->where( 'main_table.' .  $this->getIdFieldName() . ' = ' . $this->getIdFieldName());

        $updateCondition = ' EXISTS(' . $checkSelect . ') ';
        $affectedRows = $this->_getWriteAdapter()->update($this->getMainTable(), $updateData, $updateCondition);
        if (!$affectedRows) {
            $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        }

        $this->unserializeFields($object);
        $this->_afterSave($object);

        return $this;
    }


}
